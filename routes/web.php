<?php

use App\Http\Controllers\SocialiteController;
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


Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// ── Authenticated — Workspace required ───────────────────────────────────────

Route::middleware(['auth', 'workspace.member'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))
        ->name('dashboard');

    // Stub route — replaced with a full Livewire controller in Module 3.
    // Registered now so FortifyServiceProvider post-auth redirects resolve.
    Route::get('/audits/{audit}', function () {
        return view('dashboard');
    })->name('audits.show');
});

require __DIR__ . '/settings.php';
