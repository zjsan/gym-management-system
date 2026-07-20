<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Walkin;
use App\Models\AttendanceLogging;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Exception;

class AttendanceController extends Controller
{
  
    /**
     * Display a listing of today's attendance logs (Live Feed).
     */
    public function index(): JsonResponse
    {
        $logs = AttendanceLogging::with(['member', 'walkin', 'recorder'])
            ->whereDate('check_in', Carbon::today())
            ->orderBy('check_in', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Store a newly created attendance log in storage (Hybrid QR & Manual).
     * Replaces checkIn()
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $recorderId = Auth::id();
            $checkInTime = now();

            if (in_array($validated['entry_method'], ['qr_scan', 'manual_member'])) {
                $member = Member::where('membership_no', $validated['membership_no'])->first();

                if (!$member) {
                    return response()->json(['error' => 'Invalid membership number. Member not found.'], 404);
                }

                if (!$member->is_active || ($member->membership_end && Carbon::parse($member->membership_end)->isPast())) {
                    return response()->json([
                        'error' => 'Access Denied. This membership has expired or is inactive.',
                        'member_name' => "{$member->first_name} {$member->last_name}"
                    ], 422);
                }

                $log = AttendanceLogging::create([
                    'recorded_by' => $recorderId,
                    'member_id' => $member->id,
                    'walkin_id' => null,
                    'entry_method' => $validated['entry_method'],
                    'check_in' => $checkInTime,
                ]);

                return response()->json([
                    'message' => 'Member checked in successfully.',
                    'type' => 'member',
                    'name' => "{$member->first_name} {$member->last_name}",
                    'photo_path' => $member->photo_path,
                    'log' => $log->load('member')
                ], 201);
            }

            if ($validated['entry_method'] === 'manual_walkin') {
                $walkin = Walkin::create([
                    'name' => $validated['walkin_name'],
                ]);

                $log = AttendanceLogging::create([
                    'recorded_by' => $recorderId,
                    'member_id' => null,
                    'walkin_id' => $walkin->id,
                    'entry_method' => 'manual_walkin',
                    'check_in' => $checkInTime,
                ]);

                return response()->json([
                    'message' => 'Walk-in recorded successfully.',
                    'type' => 'walkin',
                    'name' => $walkin->name,
                    'log' => $log->load('walkin')
                ], 201);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the check-in.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

    }

    /**
     * Search members dynamically for autocomplete lookup.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $members = Member::where('membership_no', 'LIKE', "%{$query}%")
            ->orWhere('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'membership_no', 'first_name', 'last_name', 'photo_path', 'is_active']);

        return response()->json($members);
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
