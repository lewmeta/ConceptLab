<?php

namespace App\Services;

use App\Enums\AuditStatus;
use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Models\Audit;
use App\Models\Finding;
use App\Models\FindingHeuristic;
use App\Models\Heuristic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The single source of truth for the SBA pattern engine.
 *
 * Responsibilities:
 *   1. Accept a Draft Audit
 *   2. Run all live heuristics against the frozen input_text
 *   3. Persist Finding rows with validated char offsets
 *   4. Persist FindingHeuristic pivot rows for each fired rule
 *   5. Compute and store the sharpness_score
 *   6. Advance the Audit to Diagnosed status
 *
 * This service is framework-agnostic at the boundary.
 * It does not know it is called from Livewire. It never will.
 * No HTTP, no Request, no Response — pure domain logic.
 *
 * Module 1 scope: pattern engine only. No AI.
 * Job A (AI safety net) is wired in Module 8.
 *
 * Transaction guarantee:
 *   Both status advances and all findings persistence are inside one
 *   DB::transaction(). If anything fails, the entire run rolls back
 *   and the audit stays at Draft — clean retry, no stuck states.
 */
class LogicAuditor
{
    private ?Collection $heuristics = null;

    // ── Public entry point ────────────────────────────────────────────────

    /**
     * Run the full audit pipeline on a Draft audit
     * Returns the refreshed Audit in Diagnosed status.
     * 
     * @throws \LogicException if the audit is not in Draft status
     */
    public function run(Audit $audit): Audit
    {
        if ($audit->status !== AuditStatus::Draft) {
            throw new \LogicException(
                "LogicAuditor::run() expects a Draft audit. Got: [{$audit->status->value}]"
            );
        }

        DB::transaction(function () use ($audit) {
            // draft → analyzing — stamps analyzing_started_at
            $audit->advance();

            $matches = $this->detectMatches($audit->input_text);
            $spans   = $this->mergeOverlappingSpans($matches);
            $this->persistFindings($spans, $audit);

            $audit->update(['sharpness_score' => $this->computeScore($audit)]);

            // analyzing → diagnosed — stamps diagnosed_at
            $audit->advance();
        });

        return $audit->fresh();
    }

    // ── Heuristic loading ─────────────────────────────────────────────────

    /**
     * Load live heuristics on first use.
     * Lazy so tests can seed after the singleton is registered.
     */
    private function heuristics(): Collection
    {

        if ($this->heuristics === null) {
            $this->heuristics = Heuristic::live()
                ->orderBy('rule_number')
                ->get();
        }

        return $this->heuristics;
    }

    /**
     * Flush the cached heuristic collection.
     * Called in tests after seeding to force a fresh load.
     */
    public function flushCache(): void
    {
        $this->heuristics = null;
    }

    // ── Detection ─────────────────────────────────────────────────────────

    /**
     * Run every live heuristics against the text.
     * Returns a flat collection of raw match arrays - no DB writes yet.
     * 
     * Each match:
     * [
     *   'start_char'      => int,
     *   'end_char'        => int,
     *   'excerpt'         => string,
     *   'heuristic_id'    => int,
     *   'confusion_type'  => ConfusionType,
     *   'severity_weight' => int,
     * ]
     */
    private function detectMatches(string $text): Collection
    {
        $matches = collect();

        foreach ($this->heuristics() as $heuristic) {
            $logic = $heuristic->trigger_logic; // cast to array by the model

            foreach ($logic['keywords'] ?? [] as $keyword) {
                foreach ($this->findAllOccurrences($text, $keyword) as [$start, $end]) {
                    $matches->push($this->matchArray($text, $start, $end, $heuristic));
                }
            }

            foreach ($logic['vague_patterns'] ?? [] as $pattern) {
                foreach ($this->regexScan($text, $pattern) as [$start, $end]) {
                    $matches->push($this->matchArray($text, $start, $end, $heuristic));
                }
            }

            foreach ($logic['acting_abstracts'] ?? [] as $abstract) {
                foreach ($this->findAllOccurrences($text, $abstract) as [$start, $end]) {
                    $extended = $this->extendToSentenceEnd($text, $end);
                    $matches->push($this->matchArray($text, $start, $extended, $heuristic));
                }
            }
        }

        return $matches->filter(
            fn($m) => $m['end_char'] > $m['start_char']
                && mb_strlen(trim($m['excerpt'])) > 0
        );
    }

    private function matchArray(string $text, int $start, int $end, Heuristic $heuristic): array
    {
        return [
            'start_char'      => $start,
            'end_char'        => $end,
            'excerpt'         => mb_substr($text, $start, $end - $start),
            'heuristic_id'    => $heuristic->id,
            'confusion_type'  => $heuristic->confusion_type,
            'severity_weight' => $heuristic->severity_weight,
        ];
    }

    // ── Span merging ──────────────────────────────────────────────────────

    /**
     * Merge raw matches into consolidated Finding spans.
     *
     * Spans within 10 chars of each other are merged.
     * Multiple heuristics on the same region become one Finding
     * with multiple FindingHeuristic pivot rows.
     *
     * Returns array of:
     * [
     *   'start_char' => int,
     *   'end_char'   => int,
     *   'heuristics' => array of match arrays,
     * ]
     */
    private function mergeOverlappingSpans(Collection $matches): array
    {
        if ($matches->isEmpty()) {
            return [];
        }

        $sorted  = $matches->sortBy('start_char')->values();
        $merged  = [];
        $current = null;

        foreach ($sorted as $match) {
            if ($current === null) {
                $current = [
                    'start_char' => $match['start_char'],
                    'end_char'   => $match['end_char'],
                    'heuristics' => [$match],
                ];
                continue;
            }

            if ($match['start_char'] <= $current['end_char'] + 10) {
                $current['end_char']     = max($current['end_char'], $match['end_char']);
                $current['heuristics'][] = $match;
            } else {
                $merged[]  = $current;
                $current   = [
                    'start_char' => $match['start_char'],
                    'end_char'   => $match['end_char'],
                    'heuristics' => [$match],
                ];
            }
        }

        if ($current !== null) {
            $merged[] = $current;
        }

        return $merged;
    }

    // ── Persistence ───────────────────────────────────────────────────────

    /**
     * Persist merged spans as Finding + FindingHeuristic rows.
     *
     * The dominant confusion type and severity are taken from the
     * highest severity_weight heuristic in the span.
     * Offsets are clamped to input_char_count as a safety net —
     * Finding::booted() will reject anything still invalid.
     */
    private function persistFindings(array $spans, Audit $audit): void
    {
        foreach ($spans as $span) {
            $dominant  = collect($span['heuristics'])
                ->sortByDesc('severity_weight')
                ->first();

            $startChar = $span['start_char'];
            $endChar   = min($span['end_char'], $audit->input_char_count - 1);

            if ($endChar <= $startChar) {
                continue;
            }

            $finding = Finding::create([
                'audit_id'               => $audit->id,
                'start_char'             => $startChar,
                'end_char'               => $endChar,
                'excerpt'                => mb_substr($audit->input_text, $startChar, $endChar - $startChar),
                'primary_confusion_type' => $dominant['confusion_type'],
                'severity'               => FindingSeverity::fromWeight($dominant['severity_weight']),
                'status'                 => FindingStatus::Open,
            ]);

            // Deduplicate pivot rows — one per heuristic per finding
            $seen = [];
            foreach ($span['heuristics'] as $match) {
                $key = $match['heuristic_id'];
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                FindingHeuristic::create([
                    'finding_id'      => $finding->id,
                    'heuristic_id'    => $match['heuristic_id'],
                    'triggered_by_ai' => false,
                    'trigger_score'   => null,
                    'trigger_excerpt' => $match['excerpt'],
                ]);
            }
        }
    }


    // ── Scoring ───────────────────────────────────────────────────────────

    /**
     * Compute a 0–100 sharpness score.
     *
     * Formula (V1):
     *   penalty = sum of severity_weight across all fired heuristics
     *   score   = max(0, 100 - (penalty × 4))
     *
     * Calibration targets:
     *   Clean text (0 findings)  → 100
     *   5 critical (weight 3)    → ~40  (blunt state, typical on first pass)
     *   Post-Forum sharpening    → 85–92 (Module 6)
     */
    private function computeScore(Audit $audit): float
    {
        $penalty = FindingHeuristic::query()
            ->join('findings', 'findings.id', '=', 'finding_heuristics.finding_id')
            ->join('heuristics', 'heuristics.id', '=', 'finding_heuristics.heuristic_id')
            ->where('findings.audit_id', $audit->id)
            ->sum('heuristics.severity_weight');

        return round(max(0, 100 - ($penalty * 4)), 2);
    }

    // ── Text scanning helpers ─────────────────────────────────────────────

    /**
     * Find all case-insensitive occurrences of a keyword.
     * Returns [[start, end], ...] as mb char offsets.
     * Extends each match 60 chars forward for reading context.
     */
    private function findAllOccurrences(string $text, string $keyword): array
    {
        $positions = [];
        $lower     = mb_strtolower($text);
        $lowerKey  = mb_strtolower($keyword);
        $keyLen    = mb_strlen($lowerKey);
        $textLen   = mb_strlen($text);
        $offset    = 0;

        while (($pos = mb_strpos($lower, $lowerKey, $offset)) !== false) {
            $positions[] = [$pos, min($textLen, $pos + $keyLen + 60)];
            $offset      = $pos + $keyLen;
        }

        return $positions;
    }

    /**
     * Run a PCRE regex against the text.
     * Patterns in trigger_logic are stored without delimiters.
     * Invalid patterns are skipped silently — a malformed rule
     * must never crash an audit run.
     * Returns [[start, end], ...] as mb char offsets.
     */
    private function regexScan(string $text, string $pattern): array
    {
        $positions = [];

        try {
            $compiled = '/' . $pattern . '/iu';

            if (@preg_match($compiled, '') === false) {
                return [];
            }

            if (! preg_match_all($compiled, $text, $matches, PREG_OFFSET_CAPTURE)) {
                return [];
            }

            $textLen = mb_strlen($text);

            foreach ($matches[0] as [$matchText, $byteOffset]) {
                $start       = mb_strlen(substr($text, 0, $byteOffset));
                $end         = min($textLen, $start + mb_strlen($matchText) + 40);
                $positions[] = [$start, $end];
            }
        } catch (\Throwable) {
            // Malformed pattern — never crash the run
        }

        return $positions;
    }

    /**
     * Extend an offset to the end of the current sentence.
     * Looks ahead up to 120 chars for . ! ? — falls back to the limit.
     */
    private function extendToSentenceEnd(string $text, int $from): int
    {
        $textLen = mb_strlen($text);
        $maxLook = min($textLen, $from + 120);
        $slice   = mb_substr($text, $from, $maxLook - $from);

        if (preg_match('/[.!?]\s/u', $slice, $m, PREG_OFFSET_CAPTURE)) {
            return $from + mb_strlen(substr($slice, 0, $m[0][1])) + 2;
        }

        return $maxLook;
    }
}
