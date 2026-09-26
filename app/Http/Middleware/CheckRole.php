<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please sign in to access this feature.');
        }

        $user = auth()->user();

        // Admin has superuser privileges across all routes
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, "Access Denied: Your account role ({$user->role_label}) is not authorized to perform this operation.");
    }
}
