<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate the request
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            //activate session lifetime based on remember me
            if ($remember) {
                Session::put('auth_session_lifetime', config('session.remember_me_lifetime'));
            } else {
                Session::put('auth_session_lifetime', config('session.lifetime'));
            }

            // Redirect based on user role
            if (
                Auth::check() &&
                Auth::user()->role &&
                in_array(Auth::user()->role->name, ['master', 'seller'])
            ) {
                return redirect()->intended('/dashboard')
                    ->with('success', 'Welcome back, Admin ' . Auth::user()->name . '!');
            } elseif (
                Auth::user()->role && Auth::user()->role->name === 'buyer'
            ) {
                return redirect()->intended('/home')
                    ->with('success', 'Welcome back, Buyer ' . Auth::user()->name . '!');
            } else {
                return redirect()->intended('/error')
                    ->withErrors(['role' => 'Your account does not have a valid role assigned. Please contact support.']);
            }
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Log the user in
        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Welcome, ' . $user->name . '!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
