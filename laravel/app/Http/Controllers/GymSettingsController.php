<?php

namespace App\Http\Controllers;


use App\Models\GymSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GymSettingsController extends Controller
{
    /**
     *Public/Staff access to fetch current active fees
     */
    public function index()
    {
        //
        $settings = GymSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    /**
     * Admin-only access to update gym fees
     */
    public function store(Request $request)
    {
        //check role privellege 
        Gate::authorize("admin-only");

        $validated = $request->validate([
            'walkin_daily_fee'       => 'required|numeric|min:0',
            'monthly_membership_fee' => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            GymSetting::where('key', $key)->update([
                'value'      => $value,
                'updated_by' => $request->user()->id,
            ]);
        } 
        
        return response()->json(['message' => 'Gym fees updated successfully.']);

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
