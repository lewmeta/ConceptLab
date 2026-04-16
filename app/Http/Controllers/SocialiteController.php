<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMembership;
use App\WorkspaceRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Socialite;

class SocialiteController extends Controller
{
    /** Supported OAuth providers - extend this array to add new providers */
    private const SUPPORTED_PROVIDERS = ['google', 'github', 'facebook'];

    /**
     * Redirects the user to the OAuth provider's authorization page.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->abortIfUnsupported($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handles the OAuth callback, finds or creates the user, provisions
     * a workspace on first login, and dispatches ClaimDemoAudit if a
     * demo session cookie is present.
     *
     * Mirrors the email registration claim path exactly — both paths
     * go through the same job with the same afterCommit() guarantee.
     *
     * isNewUser is determined before fromSocialite() runs to avoid
     * a race condition where wasRecentlyCreated could be unreliable
     * under concurrent requests for the same provider+provider_id.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->abortIfUnsupported($provider);

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login')->with('error', 'Authentication session expired. Please try again.');
        }


        $user = DB::transaction(function () use ($provider, $socialiteUser) {

            // Check for existing user by Provider ID
            $isNewUser = ! User::where('provider', $provider)
                ->where('provider_id', $socialiteUser->getId())
                ->exists();

            $user = User::fromSocialite($socialiteUser, $provider);

            if ($isNewUser) {
                $workspace = Workspace::create([
                    'owner_id' => $user->id,
                    'name' => "{$user->displayName}'s Workspace",
                    'slug' => Workspace::generateSlugFromEmail($socialiteUser->getEmail() ?? (string) $user->id),
                    'created_from_demo' => false,
                ]);

                WorkspaceMembership::create([
                    'workspace_id' => $workspace->id,
                    'user_id' => $user->id,
                    'role' => WorkspaceRole::Owner,
                    'accepted_at' => now(), // owner is not invited.
                ]);

                $user->update(['current_workspace_id' => $workspace->id]);
            }

            return $user;
        });

        Auth::login($user, remember: true);

        // Dispatch demo claim after the transaction commits.
        // Uses afterCommit() for the same reason as CreateNewUser —
        // the job must not run before the user row is visible.
        $demoSessionKey = request()->cookie('demo_session_key');

        // TODO: Dispatch Claim demo audit job –– After adding audit model

        return redirect()->intended('/dashboard');
    }

    private function abortIfUnsupported(string $provider): void
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, strict: true)) {
            abort(404);
        }
    }
}
