<?php

namespace App\Http\Controllers;


use App\Models\GymSetting;
use App\Models\GymSettingLog;
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
     * Retrieve paginated audit history of setting changes.
     */
    public function auditHistory()
    {
        Gate::authorize('admin-only');

        $logs = GymSettingLog::with('updatedBy:id,first_name,last_name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 records per page

        return response()->json($logs);
    }

    /**
     * 
     */
    public function store(Request $request)
    {
    

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Admin-only access to update gym fees and create audit log entries.
     */
    public function update(Request $request)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'walkin_daily_fee'      => 'required|numeric|min:0',
            'monthly_membership_fee' => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $newValue) {
            $setting = GymSetting::where('key', $key)->first();

            if ($setting) {
                $oldValue = $setting->value;

                // Log entry is created ONLY if the rate actually changed
                if ((float) $oldValue !== (float) $newValue) {
                    // 1. Update current live active setting
                    $setting->update([
                        'value'      => $newValue,
                        'updated_by' => $request->user()->id,
                    ]);

                    // 2. Record immutable historical audit log
                    GymSettingLog::create([
                        'gym_setting_id' => $setting->id,
                        'key'            => $key,
                        'old_value'      => $oldValue,
                        'new_value'      => $newValue,
                        'updated_by'     => $request->user()->id,
                        'created_at'     => now(),
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Gym fees updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
