<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;    
use Illuminate\Support\Facades\Hash;
use App\Models\Role;   

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Returns users with their role object attached
        return response()->json(User::with('role')->get());//eager loading the slug of the role
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'role'     => 'required|exists:roles,slug', // Validate that the slug exists in roles table
        ]);

        //validating and cleaning inputs
        $validated = array_map(function($item){
            $clean_first_name = trim($item['first_name']);
            $clean_last_name = trim($item['last_name']);
            $clean_email = filter_var(strtolower(trim($item['email'])),FILTER_SANITIZE_EMAIL);

            return [
                'first_name' => $clean_first_name,
                'last_name' => $clean_last_name,
                'email' => $clean_email,
                'password' => $item['password'],
                'role' =>   $item['role']
            ];

        },$validated);

        // 1. Find the Role ID based on the slug sent from Vue
        $role = Role::where('slug', $validated['role'])->firstOrFail();

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id, // Store the role ID in the users table
        ]);

        //load the role relationship to return the user with its role data
        return response()->json($user->load('role'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
