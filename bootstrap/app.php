<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureWorkspaceMember;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin subdomain routes registered on their own domain.
            // In local development both web.php and admin.php are accessible
            // from the same domain — the subdomain constraint is enforced in
            // production only via the config('app.domain') value.
            Route::middleware('web')
                ->domain('admin.', config('app.domain'))
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'workspace.member' => EnsureWorkspaceMember::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force JSON error responses for API routes and JSON-expecting clients.
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
