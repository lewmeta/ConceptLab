<?php

namespace App\Http\Middleware;

use App\Models\WorkspaceMembership;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Confirms the authenticated user has an accepted membership in their
 * current_workspace_id. Prevents a user from accessing workspace resources
 * after being removed from a workspace before their session expires.
 *
 * Register in bootstrap/app.php:
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['workspace.member' => EnsureWorkspaceMember::class]);
 *   })
 *
 * Apply to routes:
 *   Route::middleware(['auth', 'verified', 'workspace.member'])->group(...)
 */
class EnsureWorkspaceMember
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->current_workspace_id) {
            // No workspace set — redirect to onboarding or workspace creation.
            return redirect()->route('login');
        }

        $membership = WorkspaceMembership::where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id)
            ->whereNotNull('accepted_at')
            ->first();

        if (! $membership) {
            // User has been removed from this workspace.
            // Clear the stale current_workspace_id and redirect.
            $user->update(['current_workspace_id' => null]);

            return redirect()->route('workspace.index')
                ->with('error', 'Your access to that workspace has been revoked');
        }

        // Make the membership available to controllers and Livewire components
        // via the request for this cycle — avoids a second DB hit.
        $request->merge(['current_membership' => $membership]);

        return $next($request);
    }
}
