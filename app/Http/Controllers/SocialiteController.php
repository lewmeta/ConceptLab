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
     * Handle the OAuth provider callback, authenticate or provision the user, and redirect to the intended location.
     *
     * Finds or creates a local user for the given provider, logs the user in, provisions a workspace for first-time users,
     * and redirects to the intended URL (falls back to `/dashboard`). If the OAuth state is invalid, redirects to `/login`
     * with an error message.
     *
     * @param string $provider OAuth provider identifier (e.g., 'google', 'github', 'facebook').
     * @return \Illuminate\Http\RedirectResponse A redirect response to the intended URL (falls back to '/dashboard').
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

    /**
     * Abort the request with HTTP 404 when the given OAuth provider is not in the allowed list.
     *
     * @param string $provider The OAuth provider name (must be one of self::SUPPORTED_PROVIDERS, e.g. 'google', 'github', 'facebook').
     */
    private function abortIfUnsupported(string $provider): void
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, strict: true)) {
            abort(404);
        }
    }
}
