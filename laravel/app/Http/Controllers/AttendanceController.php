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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
