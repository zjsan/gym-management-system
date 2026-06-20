<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;   
use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        try {
            //  Direct JSON wrapper. 
            return response()->json(Member::latest()->get(), 200);
        } catch (Exception $e) {
            Log::error("Failed fetching members: " . $e->getMessage());
            return response()->json(['error' => 'Unable to retrieve members.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();
        $uploadedPath = null;

        try {
            return DB::transaction(function () use ($request, $validated, &$uploadedPath) {
                // Production MVP Fix for 'membership_no':
                // Instead of looking up the last row (unsafe), use a fallback hash or timestamp 
                // Alternatively, let the database save first, then generate it. 
                // For a robust atomic MVP step, we can base it safely on a timestamp sequence or clean increment block:
                $microtime = substr(now()->format('u'), 0, 4);
                $validated['membership_no'] = 'GYM-' . date('Ymd') . '-' . $microtime;

                if ($request->hasFile('photo')) {
                    $uploadedPath = $request->file('photo')->store('members', 'public');
                    $validated['photo_path'] = $uploadedPath;
                }

                $validated['membership_start'] = now();
                $validated['membership_end'] = now()->addDays(30);
                $validated['is_active'] = true;

                $member = Member::create($validated);

                return response()->json($member, 201);
            });
        } catch (Exception $e) {
            // Delete uploaded file if DB engine fails to commit
            if ($uploadedPath) {
                Storage::disk('public')->delete($uploadedPath);
            }
            Log::error("Member creation failed: " . $e->getMessage());
            return response()->json(['error' => 'Could not create member. Server error.'], 500);
        }
    }
    
    public function toggleStatus(Member $member)
    {
        try {
            $member->is_active = !$member->is_active;
            $member->save();

            return response()->json([
                'message' => 'Status updated successfully.',
                'is_active' => $member->is_active,
                'member' => $member
            ], 200);
        } catch (Exception $e) {
            Log::error("Failed toggling member status ID {$member->id}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update member status.'], 500);
        }
    }

    /**
     * Flexible Day Adjustment Endpoint
     * Allows staff to increase or decrease days manually if business was shut down unexpectedly.
     */

    public function renewMembership(Member $member){

        if(!$member->can_renew){
            return response()->json([
                'error' => 'Lockout active. Members cannot renew their membership until 30 days have passed.'
            ], 422);
        }

        $member->renew();

        return response()->json([
            'message' => 'Membership successfully renewed for 30 days.',
            'member' => $member
        ], 200);
    }

    /**
     * Flexible Day Adjustment Endpoint
     * Allows staff to increase or decrease days manually if business was shut down unexpectedly.
     */

    public function adjustDays(Request $request, Member $member)
    {
        $request->validate([
            'days' => 'required|integer'
        ]);

        $member->adjust_membership($request->days);

        return response()->json([
            'message' => "Membership period adjusted by {$request->days} day(s).",
            'member' => $member
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
    public function update(StoreMemberRequest $request, Member $member)
    {
        //
        $validated = $request->validated();

        //handle photo upload if a new photo is provided
        if($request->hasFile('photo')) {

            if($member->photo_path) {
                //delete the old photo if it exists
                Storage::disk('public')->delete($member->photo_path);
            }

        //store the new photo and update the path
        $validated['photo_path'] = $request->file('photo')->store('members', 'public');
        }

        $member->update($validated); //update the member with the validated data

        return response()->json([
            'message' => 'Member updated successfully',
            'member' => $member->fresh() // Returns recalculated fields if any
        ], 200);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
 
    }
}
