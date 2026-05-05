<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->onboarding_completed_at) {
            // Prevent infinite loops if they are already heading to onboarding
            if (! $request->routeIs('onboarding')) {
                return redirect()->route('onboarding');
            }
        }

        return $next($request);
    }
}
