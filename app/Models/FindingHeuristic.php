<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['finding_id', 'heuristic_id', 'triggered_by_ai', 'trigger_score', 'trigger_excerpt',])]
class FindingHeuristic extends Pivot
{
    /**
     * Permanent forensic record.
     * This row says: "on [created_at], this span of text in this audit fired rule N."
     * No updated_at — this record is never mutated after creation.
     */
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'triggered_by_ai' => 'boolean',
            'trigger_score' => 'decimal:2',
        ];
    }

        // ─── Relationships ────────────────────────────────────────────────────

    /** The finding this heuristic fired on. */
    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    /** The heuristic rule that was triggered. */
    public function heuristic(): BelongsTo
    {
        return $this->belongsTo(Heuristic::class);
    }
}
