<?php

namespace App\Models\Concerns;

use App\Models\Scopes\WorkspaceScope;

/**
 * Apply this trait to any Eloquent model with a workspace_id column.
 * It registers the WorkspaceScope automatically via booted().
 *
 * Models that use this trait:
 *   Audit, Finding, ForumThread, ForumMessage (via thread), AuditMcpLog
 *
 * WorkspaceMembership intentionally does NOT use this trait —
 * it is queried across workspaces during auth checks.
 */
trait HasWorkspaceScope
{
    protected static function bootHasWorkspaceScope(): void
    {
        static::addGlobalScope(new WorkspaceScope());
    }
}
