<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{

    use HasFactory;//needed for creating dummy records in the database
    
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

    /**
     * Check if membership is expired.
     * Compares against the end of the expiration day to protect member access.
     */
    public function isExpired(): bool
    {
        if (!$this->membership_end) {
            return true;
        }

        // Production Fix: Enforce expiration at the absolute end of the operational day (23:59:59)
        return now()->greaterThan($this->membership_end->endOfDay());
    }

    /**
     * Accessor: Determines if the RENEW button should be enabled.
     * Enforces the 30-day lockout window OR allows immediate renewal if already expired.
     */
    public function getCanRenewAttribute(): bool
    {
        // RULE EXCEPTION: If they are already expired, they must be allowed to renew immediately.
        if ($this->isExpired()) {
            return true;
        }

        $baselineDate = $this->last_renewal_at ?? $this->created_at;
        
        if (!$baselineDate) {
            return true;
        }
    
        // Strict checking
        return now()->diffInDays($baselineDate) >= 30;
    }

    /**
     * Business Logic: Renews membership for a fixed 30 days.
     */
    public function renew(): void
    {
        // If expired, start from today. If active, chain it onto their current expiration date.
        $base = $this->isExpired() ? now() : $this->membership_end;
        
        $this->membership_start = now()->startOfDay();
        $this->membership_end = $base->addDays(30)->endOfDay();
        
        // CRITICAL: Update the lockout tracking timestamp to right now!
        $this->last_renewal_at = now(); 
        
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

    protected function photoUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->photo_path) {
                return null;
            }

            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('public');

            return $disk->url($this->photo_path);
        });
    }
}
