<?php

namespace App\Http\Controllers;

use App\Actions\ProvisionWorkspace;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class SocialiteController extends Controller
{
    /** Supported OAuth providers - extend this array to add new providers */
    private const SUPPORTED_PROVIDERS = ['google', 'github', 'facebook'];

    public function __construct(
        private readonly ProvisionWorkspace $provision,
    ) {}

    /**
     * Redirects the user to the OAuth provider's authorization page.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->abortIfUnsupported($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handles the OAuth callback.
     *
     * User::fromSocialite() resolves three scenarios transparently:
     *   A) Returning OAuth user        → returns existing user
     *   B) Email collision             → links OAuth to existing account
     *   C) Brand new user              → creates a new user row
     *
     * ProvisionWorkspace only runs when the user is genuinely new
     * (wasRecentlyCreated). For Scenario B (email collision), the user
     * already has a workspace and ProvisionWorkspace's idempotency guard
     * will exit early regardless — but we skip the call entirely as an
     * optimisation.
     *
     * ClaimDemoAudit dispatch is handled inside ProvisionWorkspace,
     * keeping the claim path identical to the Fortify registration path.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->abortIfUnsupported($provider);

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login')->with('error', 'Authentication session expired. Please try again.');
        }

        $user = User::fromSocialite($socialiteUser, $provider);

        if ($user->wasRecentlyCreated) {
            $this->provision->execute($user, request());
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/dashboard');
    }

    private function abortIfUnsupported(string $provider): void
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, strict: true)) {
            abort(404);
        }
    }
}
