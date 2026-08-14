<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;   
use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Str;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request): JsonResponse
    {   
        $defaultPerPage = 10;

        $perPage = (int) $request->query('per_page', $defaultPerPage);

        //check if it is integer and greater than 0, and set a maximum limit of 100 to prevent abuse
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = $defaultPerPage; // fallback to default 
        }

        $search = $request->query('search');//fetch the search query parameter in the url for server-side searching
        $query = Member::query(); //defining a query
        try {

            //server-side searching by membership number and name of member
            if (!empty($search)) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->where('membership_no', 'LIKE', "%{$search}%")
                        ->orWhere('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            }  

            //apply default ordering by creation date, showing oldest entries first
            $query->orderBy('members.created_at', 'asc');

            $members = $query->paginate($perPage);

            // MemberResource::collection wraps an array of data neatly
            return MemberResource::collection($members)->response();

        } catch (Exception $e) {
            Log::error("Failed fetching members: " . $e->getMessage());
            return response()->json(['error' => 'Unable to retrieve members.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $uploadedPath = null;

        try {
            return DB::transaction(function () use ($request, $validated, &$uploadedPath) {

                if ($request->hasFile('photo')) {
                    $uploadedPath = $request->file('photo')->store('members', 'public');
                    $validated['photo_path'] = $uploadedPath;
                }

                $validated['membership_start'] = now();
                $validated['membership_end'] = now()->addDays(30);
                $validated['is_active'] = true;

                // 1. This creates the row, database generates a unique sequential ID (e.g., 42)
                // 2. The Model's booted() method triggers and sets membership_no to GYM-0042
                $member = Member::create($validated);

               return (new MemberResource($member))
                    ->response()
                    ->setStatusCode(201);   
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


     /**
     * 
     * QR Code Image Rendering Endpoint
     */

    public function getQrCode(Member $member)
    {
        // Ensure existing members without a token get one on demand
        if (!$member->qr_token) {
            $member->qr_token = Member::generateUniqueQrToken();
            $member->save();
        }

        // Generate clean SVG payload encoding the qr_token
        $qrSvg = QrCode::size(250)
            ->margin(1)
            ->errorCorrection('H') // High error-correction for fast camera scanning
            ->generate($member->qr_token);

        return response($qrSvg)->header('Content-Type', 'image/svg+xml');
    }

    public function regenerateQrToken(Member $member): JsonResponse
    {
        $member->update([
            'qr_token' => Member::generateUniqueQrToken()
        ]);

        return response()->json([
            'message' => 'QR Code successfully regenerated.',
            'qr_token' => $member->qr_token
        ]);
    }

    /**
     * Flexible Day Adjustment Endpoint
     * Allows staff to increase or decrease days manually if business was shut down unexpectedly.
     */

    public function renewMembership(Member $member): JsonResponse
    {
        try {
            if (!$member->can_renew) {
                return response()->json([
                    'error' => 'Lockout active. Members cannot renew their membership until 30 days have passed.'
                ], 422);
            }

            $member->renew();

            return response()->json([
                'message' => 'Membership successfully renewed for 30 days.',
                'member' => new MemberResource($member)
            ], 200);    
        } catch (Exception $e) {
            Log::error("Renewal failed for member ID {$member->id}: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred during renewal.'], 500);
        }
    }

    /**
     * Flexible Day Adjustment Endpoint
     * Allows staff to increase or decrease days manually if business was shut down unexpectedly.
     */

   public function adjustDays(Request $request, Member $member): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer'
        ]);

        try {
            $member->adjust_membership($request->days);

            return response()->json([
                'message' => "Membership period adjusted by {$request->days} day(s).",
                'member' => new MemberResource($member)
            ], 200);
        } catch (Exception $e) {
            Log::error("Day adjustment failed for member ID {$member->id}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to adjust membership parameters.'], 500);
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
     * Update the specified resource in storage.
     */
    public function update(StoreMemberRequest $request, Member $member): JsonResponse
    {
        $validated = $request->validated();
        $oldPhotoPath = $member->photo_path;
        $newUploadedPath = null;

        try {
            return DB::transaction(function () use ($request, $member, $validated, $oldPhotoPath, &$newUploadedPath) {
                if ($request->hasFile('photo')) {
                    $newUploadedPath = $request->file('photo')->store('members', 'public');
                    $validated['photo_path'] = $newUploadedPath;
                }

                $member->update($validated);

                // Delete the OLD photo ONLY after a successful DB operation commit
                if ($request->hasFile('photo') && $oldPhotoPath) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }

                return response()->json([
                    'message' => 'Member updated successfully',
                    'member' => new MemberResource($member->fresh())
                ], 200);
            });
        } catch (Exception $e) {
            // Cleanup failed new upload immediately
            if ($newUploadedPath) {
                Storage::disk('public')->delete($newUploadedPath);
            }
            Log::error("Failed updating member ID {$member->id}: " . $e->getMessage());
            return response()->json(['error' => 'Update transaction failed.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
 
    }
}
