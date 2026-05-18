<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;   
use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Str;

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
    public function store(StoreMemberRequest $request, Member $member)
    {
        //
        $validated = $request->validated();

        //generate unique membership number
        $lastMember = Member::latest('id')->first();
        $nextId = $lastMember ? $lastMember->id + 1 : 1; // Find the last member's ID to increment it, or start at 0

        // str_pad turns "1" into "0001"
        $validated['membership_no'] = 'GYM-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        
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

    public function toggleStatus( Member $member)
    {
        $member->is_active = !$member->is_active; // Flips between active/inactive
        $member->save();

        return response()->json([
            'message' => 'Status updated successfully.',
            'is_active' => $member->is_active, // return the new status
            'member' => $member //  return the updated member data
        ], 200);
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
