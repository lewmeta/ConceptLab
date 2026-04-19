<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Automatically filters every query to the authenticated user's
 * current_workspace_id. Applied to all models with a workspace_id column.
 *
 * Applied via the HasWorkspaceScope trait — not registered globally in
 * AppServiceProvider, so it can be bypassed cleanly with withoutGlobalScope()
 * in admin routes and background jobs that need cross-workspace access.
 *
 * Usage in models:
 *   use App\Models\Concerns\HasWorkspaceScope;
 *
 * Bypassing for admin/system queries:
 *   Audit::withoutGlobalScope(WorkspaceScope::class)->where(...)->get();
 */
class WorkspaceScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Do not apply in console/queue context where auth()->user() is null.
        // Jobs and scheduled commands must scope manually or use withoutGlobalScope().
        // if (! auth()->check()) {
        //     return;
        // }
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user();

        // $workspaceId = auth()->user()?->current_workspace_id;

        // If current_workspace_id is null (e.g. mid-onboarding), do not filter.
        // This prevents an empty result set before the workspace is assigned.

        // 2. Critical Check: If the user is logged in, they MUST be scoped.
        if ($user) {
            $workspaceId = $user->current_workspace_id;

            if ($workspaceId) {
                /** @disregard P1006 */
                // $builder->where(
                //     $model->getTable() . '.workspace_id',
                //     $workspaceId
                // );
                $builder->where($model->qualifyColumn('workspace_id'), $workspaceId);
            } else {
                // Fail safe: If they are logged in but have no workspace, 
                // show nothing rather than everything.
                $builder->whereRaw('1 = 0');
            }
        }

        // If no user is logged in (public routes), the scope does nothing.
    }
}