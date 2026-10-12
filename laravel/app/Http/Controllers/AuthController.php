<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
 
}
