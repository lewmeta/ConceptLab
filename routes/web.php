<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');

// ── OAuth — Socialite ─────────────────────────────────────────────────────────

// Provider is constrained at the route level via whereIn() — no unknown provider
// can reach the controller even without the abortIfUnsupported() guard.
Route::prefix('auth/{provider}')
    ->whereIn('provider', ['google', 'facebook', 'github'])
    ->group(function () {
        Route::get('redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');

        Route::get('callback', [SocialiteController::class, 'callback'])
            ->name('socialite.callback');
    });


Route::middleware(['auth', 'verified', 'onboarded'])->group(function () {
    Route::get('/onboarding', function () {
        $user = Auth::user();

        // If onboarding is already complete, send them to their workspace.
        // Prevents revisiting the overlay on back-navigation.
        if ($user->hasCompletedOnboarding) {
            return redirect()->route('dashboard');
        }

        // If somehow the user has no workspace yet (edge case: OAuth race),
        // Send them to the dashboard which will trigger workspace creation.
        if (! $user->current_workspace_id) {
            return redirect()->route('dashboard');
        }

        return view('pages.onboarding.index');
    })->name('onboarding');
});

// ── Authenticated — Workspace required ───────────────────────────────────────

Route::middleware(['auth', 'workspace.member', 'onboarded'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))
        ->name('dashboard');

    // Stub route — replaced with a full Livewire controller in Module 3.
    // Registered now so FortifyServiceProvider post-auth redirects resolve.
    Route::get('/audits/{audit}', function (\App\Models\Audit $audit) {
        return view('dashboard');
    })->name('audits.show');
});

require __DIR__ . '/settings.php';
