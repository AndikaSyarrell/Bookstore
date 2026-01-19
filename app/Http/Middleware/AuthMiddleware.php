<?php
// app/Http/Middleware/CustomAuthMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // Store the intended URL
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Redirect to login page with intended URL
            return redirect()->guest(route('login'))
                ->with('error', 'Please login to access this page.');
        }

        // Optional: Check if user is active (if you have an 'active' column)
        // if (!Auth::user()->active) {
        //     Auth::logout();
        //     return redirect()->route('login')
        //         ->with('error', 'Your account has been deactivated.');
        // }

        return $next($request);
    }
}