<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;   

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(Member::all());//eager loading the slug of the role
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validated();

        // Logic for photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('members', 'public');
            $validated['photo_path'] = $path;
        }
        
        // Set initial 30 days membership
        $validated['membership_start'] = now();
        $validated['membership_end'] = now()->addDays(30);

        // Create the member record
        $member = Member::create($validated);

        return response()->json($member, 201);
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
