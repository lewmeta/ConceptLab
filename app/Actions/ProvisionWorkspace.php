<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Workspace;
use App\Models\WorkspaceMembership;
use App\Enums\WorkspaceRole;
use App\Jobs\ClaimDemoAudit;
use Illuminate\Support\Facades\Log;

/**
 * Single source of truth for workspace provisioning after user creation.
 *
 * Called by:
 *   - CreateNewUser (Fortify email/password registration)
 *   - SocialiteController (OAuth callback for new users)
 *
 * Responsibilities:
 *   1. Create the user's first Workspace
 *   2. Create the owner WorkspaceMembership
 *   3. Set current_workspace_id on the User
 *   4. Dispatch ClaimDemoAudit if a demo session cookie is present
 *
 * Idempotent: if the user already has a workspace (e.g. a retry or race
 * condition), returns early without creating a duplicate.
 *
 * Workspace naming and slug generation live here only. If the convention
 * changes, this is the one place to update.
 */
class ProvisionWorkspace
{
    public function execute(User $user, ?Request $request = null): void
    {
        // Guard - if the user was already provisioned, do not create a second workspace.
        if ($user->current_workspace_id !== null) {
            return;
        }


        // Dispatch demo claim after the transaction commits.
        // The job handles the case where no unclaimed demo exists gracefully.
        $demoSessionKey = $request?->cookie('demo_session_key');

        DB::transaction(function () use ($user, $request) {
            $workspace = Workspace::create([
                'owner_id' => $user->id,
                'name' => "{$user->displayName}'s Workspace",
                'slug'              => Workspace::generateSlugFromEmail(
                    $user->email ?? (string) $user->id
                ),
                'created_from_demo' => false,
            ]);

            WorkspaceMembership::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'role' => WorkspaceRole::Owner,
                'accepted_at' => now(),
            ]);

            $user->update(['current_workspace_id' => $workspace->id]);
        });


        if (filled($demoSessionKey)) {
            // Dispatch demo claim after the transaction commits.
            Log::debug("ProvisionWorkspace: Dispatching demo claim job.", ['demo_session_key' => $demoSessionKey]);
            ClaimDemoAudit::dispatch($user->id, $demoSessionKey);
        }
    }
}
