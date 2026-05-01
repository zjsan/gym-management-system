<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;    
use Illuminate\Support\Facades\Hash;
use App\Models\Role;   
use App\Http\Requests\StoreUserRequest;

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
    public function store(StoreUserRequest $request)
    {
        //retrive the clean and validated data
        $validated = $request->validated();

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
    public function update(StoreUserRequest $request, User $user)
    {
        //
        $validated = $request->validated();
        $role = Role::where('slug', $validated['role'])->firstOrFail();//fetch the role id based on the slug

        //update the user table
        $updateData =[
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'role_id'    => $role->id,
        ];

        //hash the password if it is updated
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json($user->load('role'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
            return response()->json(['message' => 'User deleted'], 200);
        }
}
