<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Member extends Model
{
    //
    protected $fillable = [
        'membership_no', 'first_name', 'last_name', 'contact_number', 'emergency_contact_number',
        'address', 'gender', 'date_of_birth', 'photo_path', 'is_active',
        'membership_start', 'membership_end'
    ];

    protected $casts = [
        'membership_start' => 'datetime',
        'membership_end' => 'datetime',
        'last_renewal_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['age', 'can_renew']; // Automatically append custom runtime properties to serialized JSON outputs

    // Check if membership is expired (based on 30-day rule)
    public function isExpired()
    {
        return now()->greaterThan($this->membership_end);
    }

    /**
     * Check if 30 days have passed since registration OR last renewal.
     * Members cannot renew early until this condition passes.
     */
    public function getCanRenewAttribute()
    {
        $baselineDate = $this->last_renewal_at ?? $this->created_at;
        
        // If there's no baseline (unsaved model instance), fallback safely to true
        if (!$baselineDate) {
            return true;
        }

        return now()->diffInDays($baselineDate) >= 30;
    }

    // Logic to renew: always adds 30 days
    public function renew()
    {
        $this->membership_start = now();

        // Always adds 30 days to their current end date if active, or from now if expired
        $base = $this->isExpired() ? now() : $this->membership_end;
        
        $this->membership_end = $base->addDays(30);
        $this->save();
    }

    //function to adjust membership end date by adding 1 day
    public function adjust_membership($days = 1)
    {
        // Use the existing end date as the starting point
        $currentEnd = $this->membership_end ?? now();
        $this->membership_end = $currentEnd->addDays($days);
        $this->save();
    }

    //function to derived age from date of birth
    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    protected static function booted()
    {
        static::created(function ($member) {
            // Automatically pads the auto-increment ID: e.g., ID 1 becomes "GYM-0001"
            $member->membership_no = 'GYM-' . str_pad($member->id, 4, '0', STR_PAD_LEFT);
            $member->saveQuietly(); // saveQuietly avoids triggering infinite boot loops
        });
    }
}
