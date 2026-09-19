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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin and Admin have universal access
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return $next($request);
        }

        // Support comma-separated or multiple arguments
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $sub) {
                $allowedRoles[] = trim($sub);
            }
        }

        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Access restricted to ' . implode(', ', $allowedRoles));
    }
}
