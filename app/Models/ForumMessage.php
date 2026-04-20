<?php

namespace App\Models;

use App\Enums\ForumMessageRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['thread_id', 'role', 'content', 'referenced_finding_id', 'sequence', 'is_opening_question', 'ai_model_used', 'token_count',])]
class ForumMessage extends Model
{
    /**
     * Permanent forensic record — the Socratic dialogue is never mutated.
     * Sequence is the authoritative ordering column, not created_at.
     * No updated_at — messages are immutable after insertion.
     */
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'role' => ForumMessageRole::class,
            'is_opening_question' => 'boolean',
            'sequence' => 'integer',
            'token_count' => 'integer',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** Whether this message was produced by the Clarification Interrogator (AI). */
    public bool $isFromInterrogator {
        get => $this->role === ForumMessageRole::Interrogator;
    }

    /** Whether this message was written by the human user. */
    public bool $isFromUser {
        get => $this->role === ForumMessageRole::User;
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /** The Forum thread this message belongs to. */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    /**
     * The specific finding this message references, if any.
     * NULL for opening questions that address the whole audit,
     * and for all user replies.
     */
    public function referencedFinding(): BelongsTo
    {
        return $this->belongsTo(Finding::class, 'referenced_finding_id');
    }
}
