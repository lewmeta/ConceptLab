<?php

// routes/admin.php — admin domain (admin.conceptlab.com)

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', fn() => view('admin.dashboard.index'))
            ->name('admin.dashboard');
    });

    // Heuristic management — append-only, publish/deactivate only.
    // Implemented in Module 8 (admin dashboard scope).
    // Route::get('/heuristics', fn() => view('admin.heuristics.index'))
    // ->name('admin.heuristics.index');