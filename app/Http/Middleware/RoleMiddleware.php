<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  The list of required roles (e.g., 'admin', 'accounting')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Check if a user is logged in
        if (!Auth::check()) {
            // Redirect unauthenticated users to the login page
            return redirect('/login');
        }

        $user = Auth::user();

        // IMPORTANT: This assumes your User model has a 'role' column (e.g., string)
        // If your roles are handled differently (e.g., a many-to-many relationship), 
        // you will need to adjust this check.

        // 2. Check if the user's role is in the list of allowed roles
        if (!in_array($user->role, $roles)) {
            // User does not have the required role, return a 403 Forbidden response
            return response()->view('errors.403', [], 403);
        }

        return $next($request);
    }
}
