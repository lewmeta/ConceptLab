<?php

namespace App\Models;

use App\Enums\WorkspaceRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['workspace_id', 'user_id', 'role', 'invited_by', 'accepted_at'])]
class WorkspaceMembership extends Pivot
{
    protected $table = 'workspace_memberships';
    protected function casts(): array
    {
        return [
            'role' => WorkspaceRole::class,
            'accepted_at' => 'datetime',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** Whether the user has accepted this membership invitation */
    public bool $hasAccepted {
        get => $this->accepted_at !== null;
    }

    /** Whether this invitation is still awaiting acceptance */
    public bool $isPending {
        get => $this->accepted_at === null;
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /** The workspace this membership grants access to */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /** The user who holds this membership */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who sent this invitation
     * NULL for the workspace owner's self-created membership
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
