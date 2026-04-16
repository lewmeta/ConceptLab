<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

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

require __DIR__ . '/settings.php';
