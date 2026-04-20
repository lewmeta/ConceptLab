<?php

namespace App\Models;

use App\Enums\ForumThreadStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['audit_id', 'workspace_id', 'status', 'question_count', 'user_response_count', 'resolution_summary'])]
class ForumThread extends Model
{
    protected function casts(): array
    {
        return [
            'status'              => ForumThreadStatus::class,
            'question_count'      => 'integer',
            'user_response_count' => 'integer',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /**
     * Whether the Forum thread is still accepting new messages.
     * Delegates to the ForumThreadStatus enum's isActive() method.
     */
    public bool $isActive {
        get => $this->status->isActive();
    }

    /**
     * Whether a resolution summary has been generated and stored.
     * Used by the UI to conditionally display the Forum outcome panel.
     */
    public bool $hasResolution {
        get => filled($this->resolution_summary);
    }
 
    // ─── Relations ────────────────────────────────────────────────────────                   

    /** The audit this Forum thread belongs to. */
    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    /** The workspace this thread belongs to (denormalised for scoped queries). */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * All messages in this thread, ordered by sequence.
     * Sequence is the authoritative ordering — not created_at.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ForumMessage::class, 'thread_id')
            ->orderBy('sequence');
    }
}
