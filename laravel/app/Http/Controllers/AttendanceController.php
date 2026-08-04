<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Member;
use App\Models\Walkin;
use App\Models\AttendanceLogging;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

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
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $recorderId = Auth::id();
            $checkInTime = now();

            //member lookup for both QR scan and manual member entry
            if (in_array($validated['entry_method'], ['qr_scan', 'manual_member'])) {

                $inputNo = trim($validated['membership_no']);

                // Standardize the input, Ensure 'GYM-' prefix is attached if user typed just '0001' or '1'
                $formattedNo = $inputNo;
                if (!str_starts_with(strtoupper($inputNo), 'GYM-')) {
                    // Extract numbers and pad to 4 digits if needed (e.g., '1' -> '0001')
                    $digitsOnly = preg_replace('/\D/', '', $inputNo);
                    if (!empty($digitsOnly)) {
                        $formattedNo = 'GYM-' . str_pad($digitsOnly, 4, '0', STR_PAD_LEFT);
                    }
                }

                // Strict, exact match only: Check raw input first, then formatted version
                $member = Member::where('membership_no', $inputNo)
                    ->orWhere('membership_no', $formattedNo)
                    ->first();

                if (!$member) {
                    return response()->json(['error' => 'Invalid membership number. Member not found.'], 404);
                }

                if (!$member->is_active) {
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
        $query = trim($request->query('query', ''));

        if (empty($query)) {
            return response()->json([]);
        }

        $members = Member::searchQuery($query)
            ->limit(5)
            ->get();

        return MemberResource::collection($members);
    }

    /**
     * Look up a member by membership number or ID for pre-check-in verification.
     */
     public function lookup(Request $request): JsonResponse
     {
        try {
            $query = $request->query('query', '');

            if (empty(trim($query))) {
                return response()->json(['message' => 'Please provide a search query.'], 400);
            }

            $member = Member::searchQuery($query)->first();

            if (!$member) {
                return response()->json(['message' => 'Member not found.'], 404);
            }

            return new MemberResource($member);

        } catch (\Exception $e) {
            Log::error('Member lookup error: ' . $e->getMessage());

            return response()->json([
                'message' => 'An internal error occurred during member lookup.'
            ], 500);
        }
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
