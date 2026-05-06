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
     * Resolve the redirect target after a successful login.
     *
     * Flow:
     *   1. Onboarding not completed → /onboarding
     *      (edge case: OAuth user who somehow skipped onboarding)
     *   2. Most recent diagnosed audit exists → /audits/{id}
     *      (returning user lands directly in their work)
     *   3. No diagnosed audit → /dashboard
     *      (returning user with no prior audits)
     *
     * Note: Audit query is scoped by WorkspaceScope to current_workspace_id
     * automatically — no explicit workspace filter needed here.
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
