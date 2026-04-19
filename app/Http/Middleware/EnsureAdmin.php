<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route group to users with UserRole::Admin.
 * Used to protect the admin dashboard domain.
 *
 * Register in bootstrap/app.php:
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['admin' => EnsureAdmin::class]);
 *   })
 *
 * Apply to routes:
 *   Route::middleware(['auth', 'admin'])->group(...)
 */
class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== UserRole::Admin) {
            abort(403, 'Access restricted to system administrator');
        }
        return $next($request);
    }
}
