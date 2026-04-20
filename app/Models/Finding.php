<?php

namespace App\Models;

use App\Enums\ConfusionType;
use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['audit_id', 'start_char', 'end_char', 'excerpt', 'primary_confusion_type', 'severity', 'status', 'repair_text', 'repair_requested_at', 'repair_generated_at', 'dismissed_at', 'dismissed_reason',])]
class Finding extends Model
{
    protected function casts(): array
    {
        return [
            'primary_confusion_type' => ConfusionType::class,
            'severity' => FindingSeverity::class,
            'status' => FindingStatus::class,
            'repair_requested_at' => 'datetime',
            'repair_generated_at' => 'datetime',
            'dismissed_at' => 'datetime',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /**
     * Whether this finding is in a terminal state (repaired or dismissed)
     * Delegates to the FindingStatus enum for the definition of resolved.
     */
    public bool $isResolved {
        get => $this->status->isResolved();
    }

    /**
     * Whether a repair has been requested but not yet generated
     * Used by the UI to show a loading state on the repair button.
     */
    public bool $hasPendingRepair {
        get => $this->repair_requested_at !== null
            && $this->repair_generated_at === null;
    }

    /** The character length of the highlighted span in the audit text. */
    public int $spanLength {
        get => $this->end_char - $this->start_char;
    }

    // ─── Integrity Enforcement ────────────────────────────────────────────

    /**
     * Validate char offsets against the parent audit before insertion.
     * All offsets reference the frozen audit.input_text — they must be
     * within its bounds and form a valid non-empty span.
     */
    public static function booted(): void
    {
        static::creating(function (Finding $finding) {
            $audit = Audit::withoutGlobalScopes()->find($finding->audit_id);

            if (! $audit) {
                throw new \LogicException("Finding references non-existent audit.");
            }

            if ($finding->end_char <= $finding->start_char) {
                throw new \LogicException(
                    "Finding end_char [{$finding->end_char}] must be greater than " .
                        "start_char [{$finding->start_char}]."
                );
            }

            if ($finding->end_char > $audit->input_char_count) {
                throw new \LogicException(
                    "Finding end_char [{$finding->end_char}] exceeds audit " .
                        "input_char_count [{$audit->input_char_count}]."
                );
            }
        });
    }


    // ─── Query Scopes ─────────────────────────────────────────────────────

    /** Only findings with critical severity. */
    #[Scope]
    protected function critical(Builder $query): void
    {
        $query->where('severity', FindingSeverity::Critical);
    }

    /** Only findings still open (not yet acknowledged, repaired, or dismissed). */
    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('status', FindingStatus::Open);
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /** The audit this finding belongs to. */
    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    /** All pivot rows recording which heuristics fired on this finding's span. */
    // TODO: Add a FindingHeuristic model for this pivot table to encapsulate heuristic-specific data like weight at the finding level.

    /**
     * All heuristics that fired on this finding (via pivot).
     * Use findingHeuristics() when you need trigger_score or triggered_by_ai.
     */
    // TODO

}
