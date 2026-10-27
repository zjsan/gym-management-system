<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        //validate the incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => 'required|string|min:8',
        ]);

        //Attempt authentication with email and password
        if (! Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
           
        }
        
        // Get the authenticated user
        $user = Auth::user();

        //  Check if the user's status is deactivated
        if ($user->status !== 'active') {

            // Immediately log them out and invalidate the session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Your account has been deactivated. Please contact the gym administrator.'
            ], 403); // 403 Forbidden is ideal for deactivated accounts
        }

        $request->session()->regenerate();

        // Eager load the 'role' relationship so Vue can see the 'slug'
        $user->load('role');

        // Check if user must change their temporary password
        if ($user->must_change_password) {
            return response()->json([
                'message' => 'Please update your temporary password to continue.',
                'user' => $user,
                'requires_password_change' => true, // Frontend uses this to redirect to change-password view
            ], 200);
        }

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
        ], 200);
      
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out and session invalidated.']);
    }
 

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], // Validates against user's current hashed password
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false, // Clear the force-reset flag
        ]);

        Mail::to($user->email)->send(new PasswordChangedMail($user));

        return response()->json([
            'message' => 'Password updated successfully.',
            'user' => $user->load('role'),
        ], 200);
    }

    //  Send password reset link email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Uses Laravel's built-in password broker
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    //  Handle the reset password submission
        public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'must_change_password' => false, // Clear forced reset flag if active
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }
}
