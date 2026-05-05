<?php

namespace App\Http\Responses;

use App\Enums\AuditStatus;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Override;

class LoginResponse implements LoginResponseContract
{
    /**
     * Handle the response after login.
     */
    #[Override]
    public function toResponse($request)
    {
        $user = Auth::user();

        // Check the timestamp specifically
        if (! $user->onboarding_completed_at) {
            return redirect()->route('onboarding');
        }

        $latestAudit = Audit::where('status', AuditStatus::Diagnosed)->latest()->first();

        return redirect()->intended(
            $latestAudit ? route('audits.show', $latestAudit) : route('dashboard')
        );
    }
}
