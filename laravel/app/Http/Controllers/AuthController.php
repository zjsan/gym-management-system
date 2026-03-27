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

        //if the user exists and the password is correct, generate the session
        if (Auth::attempt($credentials)) {

            //generate the session for the user
            $request->session()->regenerate();
 
            return response()->json([
                'message' => 'Login successful.'
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials.'
        ], 401);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out and session invalidated.']);
    }
 
}
