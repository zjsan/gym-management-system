<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(Request $request): RedirectResponse
    {
        //validate the incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => 'required|string|min:8',
        ]);
 
        //check if the user exists in the database
        $user = User::where($loginField, $request->login)->first();

        //if the user does not exist or the password is incorrect, return an error response
        if (! $user || ! Hash::check($request->password, $user->password)) 
        {
            
            return response()->json(['message' => 'Invalid credentials.', 401]);
           
        }

        //if the user exists and the password is correct, generate the session
        if (Auth::attempt($credentials)) {

            //generate the session for the user
            $request->session()->regenerate();
 
            return response()->json(['message' => 'Login successful.', 200]);
        }
    }
 
}
