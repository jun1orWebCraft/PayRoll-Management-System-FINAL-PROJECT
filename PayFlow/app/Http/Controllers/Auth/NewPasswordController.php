<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class NewPasswordController extends Controller
{
    // Show reset password form
    public function create($token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Handle reset password
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Find the password reset record
        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $record) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }

        // Find user or employee
        $account = User::where('email', $request->email)->first();
        if (! $account) {
            $account = Employee::where('email', $request->email)->first();
        }

        if (! $account) {
            return back()->withErrors(['email' => 'Account not found.']);
        }

        // Update password
        $account->password = Hash::make($request->password);
        $account->save();

        // Delete the password reset record
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully.');
    }
}
