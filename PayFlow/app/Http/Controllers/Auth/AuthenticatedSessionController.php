<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    // Show the shared login form
    public function create()
    {
        // If already logged in, redirect based on guard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        $response = response()->view('auth.login');

        // Prevent back-button cache
        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }


    // Handle login for both users and employees
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1️⃣ Try to log in as a regular user (default guard)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role
            if ($user->role === 'accountant') {
                return redirect()->route('accountant.dashboard');
            }

            return redirect()->route('dashboard');
        }

        // 2️⃣ If not a user, try employee guard
        if (Auth::guard('employee')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('employee.dashboard');
        }

        // 3️⃣ If both fail
        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    // Handle logout for both guards
    public function destroy(Request $request)
    {
        if (Auth::guard('employee')->check()) {
            Auth::guard('employee')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
