<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    // Step 1: Send verification code via email
    public function store(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'No user found with that email address.']);
        }

        // Generate 6-digit code
        $code = rand(100000, 999999);

        // Save code (hashed) in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        // Send code to email
        Mail::to($request->email)->send(new VerificationCodeMail($code));

        // Show verification form
        return view('auth.verify-code', ['email' => $request->email])
            ->with('status', 'A verification code has been sent to your email.');
    }

    // Step 2: Verify code and redirect to reset page
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        if (! $record || ! Hash::check($request->code, $record->token)) {
            return back()->withErrors(['code' => 'Invalid verification code.'])->withInput();
        }

        // Generate one-time reset token
        $resetToken = Str::random(64);

        DB::table('password_resets')->where('email', $request->email)->update([
            'token' => $resetToken,
            'created_at' => now(),
        ]);

        // Redirect with token + email
        return redirect()->route('password.reset', [
            'token' => $resetToken,
            'email' => $request->email,
        ]);
    }
}
