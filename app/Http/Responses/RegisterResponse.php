<?php

// app/Http/Responses/RegisterResponse.php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Override;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Resolve the redirect target after a successful registration.
     *
     * New users always go to onboarding first. The onboarding overlay
     * captures the workspace name and domain profile, then redirects
     * to the claimed audit (if ClaimDemoAudit has run) or the dashboard.
     *
     * This is always correct for registration — a user can only register
     * once, so this path is always a first-time user.
     */
    #[Override]
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('onboarding');
    }
}
