<?php

namespace App\Models;

use App\Enums\ConfusionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['rule_number', 'confusion_type', 'error_name', 'plain_name', 'trigger_logic', 'repair_template', 'forum_question_template', 'severity_weight', 'is_active', 'supersedes_id', 'published_at'])]
class Heuristic extends Model
{
    use HasFactory;

    /**
     * Heuristics are append-only. Once published, they are immutable.
     * Corrections produce successor rules via supersedes_id.
     * No softDeletes - deactivation via is_active is the only permitted change
     */

    protected function casts(): array
    {
        return [
            'confusion_type' => ConfusionType::class,
            'trigger_logic' => 'array',
            'severity_weight' => 'integer',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** A heuristic is live when it is both active and published */
    public bool $isLive {
        get => $this->is_active && $this->published_at !== null;
    }

    /** Whether this rule has at least one successor (i.e it was corrected) */
    public bool $hasSuccessor {
        get => $this->successors()->exists();
    }

    // ─── Immutability Enforcement ─────────────────────────────────────────

    /**
     * Once published, all diagnostic fields are frozen.
     * The only permitted post-publication mutation is toggling is_active.
     * To correct a published rule: deactivate it and publish a successor.
     */
    protected static function booted(): void
    {
        static::updating(function (Heuristic $heuristic) {
            if ($heuristic->getOriginal('published_at') === null) {
                // Still a draft – all edits permitted.
                return;
            }

            $immutable = ['rule_number', 'confusion_type', 'error_name', 'plain_name', 'trigger_logic', 'repair_template', 'forum_question_template', 'severity_weight', 'supersedes_id'];

            foreach ($immutable as $field) {
                if ($heuristic->isDirty($field)) {
                    throw new \LogicException(
                        "Heuristic #{$heuristic->rule_number} is published and immutable. " .

                            "Field [{$field}] cannot be changed. " .
                            "Deactivate this rule and publish a successor instead"
                    );
                }
            }
        });
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────

    /** 
     * Only rules visible to the audit engine: active and published 
     * 
     * */
    #[Scope]
    protected function live(Builder $query): void
    {
        $query->where('is_active', true)
            ->whereNotNull('published_at');
    }

    /** Only rules not yet published (safe to edit) */
    #[Scope]
    protected function draft(Builder $query): void
    {
        $query->whereNull('published_at');
    }


    // ─── Relationships ────────────────────────────────────────────────────

    /**
     * The rule this heuristic replaces.
     * NULL for original seed rules (no predecessor).
     */
    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(Heuristic::class, 'supersedes_id');
    }

    /**
     * Rules that were published to correct or replace this heuristic.
     * HasMany becuase noDB contraint precents multiple successors,
     * even though one is the expected case.
     */
    public function successors(): HasMany
    {
        return $this->hasMany(Heuristic::class, 'supersedes_id');
    }
}
