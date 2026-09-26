<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login screen.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle standard authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return redirect()->intended(route('dashboard'))
                ->with('success', "Welcome back, {$user->name}! Signed in as {$user->role_label}.");
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Fast 1-click Demo Login for testing roles
     */
    public function quickLogin($role)
    {
        $allowed = [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_STAFF, User::ROLE_AUDITOR];
        if (!in_array($role, $allowed)) {
            abort(404);
        }

        $user = User::where('role', $role)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', "No user found with role: {$role}");
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', "Switched account to {$user->name} ({$user->role_label})!");
    }

    /**
     * Log out the current user session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out successfully.');
    }
}
