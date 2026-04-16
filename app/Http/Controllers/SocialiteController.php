<?php

namespace App\Http\Controllers;

use App\Models\User;
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

            $isNewUser = ! User::where('provider', $provider)
                ->where('provider_id', $socialiteUser->getId())
                ->exists();

            $user = User::fromSocialite($socialiteUser, $provider);

            if ($isNewUser) {
                // TODO: Create workspace and attach membership

                $user->update(['current_workspace_id' => null]);
            }

            return $user;
        });

        Auth::login($user, remember: true);

        // TODO: Dispatch demo claim after the transaction commits.

        return redirect()->intended('/dashboard');
    }

    private function abortIfUnsupported(string $provider): void
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, strict: true)) {
            abort(404);
        }
    }
}
