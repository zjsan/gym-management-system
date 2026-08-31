<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Member extends Model
{

    use HasFactory;//needed for creating dummy records in the database

    //
    protected $fillable = [
        'membership_no', 'qr_token', 'first_name', 'last_name', 'contact_number', 'email',
        'emergency_contact_number',
        'address', 'gender', 'date_of_birth', 'photo_path', 'is_active',
        'membership_start', 'membership_end', 'last_renewal_at'
    ];

    protected $casts = [
        'membership_start' => 'datetime',
        'membership_end' => 'datetime',
        'date_of_birth' => 'date',
        'last_renewal_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['age', 'can_renew', 'photo_url']; // Automatically append custom runtime properties to serialized JSON outputs

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

    /**
     * Business Logic: Adjusts membership end date for gym closures.
     * Handles both positive extensions (+1d) and negative manual testing adjustments (-1d).
     */
    public function adjust_membership(int $days = 1): void
    {
        $currentEnd = $this->membership_end ?? now();
        $this->membership_end = $currentEnd->addDays($days)->endOfDay();
        
        // If manually reducing days for testing, roll back the lockout tracking
        // so that manual date testing doesn't break the 30-day "can_renew" calculations.
        if ($days < 0 && $this->last_renewal_at) {
            $this->last_renewal_at = $this->last_renewal_at->addDays($days);
        } elseif ($days < 0 && !$this->last_renewal_at && $this->created_at) {
            $this->created_at = $this->created_at->addDays($days);
        }

        $this->save();
    }

    //function to derived age from date of birth
    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Real-time computed active status based on calendar date.
     */
    public function getIsActiveAttribute($value): bool
    {

        // Calendar Date Expiration Guard
        // If today is past the membership_end date, they are inactive regardless of time.
        if ($this->membership_end && today()->greaterThan(Carbon::parse($this->membership_end)->startOfDay())) {
            return false;
        }

        return true;
    }

    protected static function booted()
    {
        static::created(function ($member) {
            // Automatically pads the auto-increment ID: e.g., ID 1 becomes "GYM-0001"
            $member->membership_no = 'GYM-' . str_pad($member->id, 4, '0', STR_PAD_LEFT);
            $member->saveQuietly(); // saveQuietly avoids triggering infinite boot loops
        });

        static::retrieved(function (Member $member) {
            // If they are marked active in DB, but their date has passed...
            if ($member->is_active && $member->expiration_date && Carbon::parse($member->expiration_date)->isPast()) {
                // Silently update the database status without firing more events
                $member->timestamps = false; // Prevent updating 'updated_at' unnecessarily
                $member->updateQuietly(['is_active' => false]);
            }
        });

        static::saving(function (Member $member) {
            // Force status to inactive if they are expired, no matter what the input try to set
            if ($member->expiration_date && Carbon::parse($member->expiration_date)->isPast()) {
                $member->is_active = false;
            }
        });

        //create a unique QR token for each member upon creation
        static::creating(function (Member $member) {
            if (empty($member->qr_token)) {
                $member->qr_token = static::generateUniqueQrToken();
            }
        });
    }

    public static function generateUniqueQrToken(): string
    {
        do {
            // Secure prefix + uppercase random string + UUID prefix
            $token = 'GYM-' . Str::upper(Str::random(8)) . '-' . Str::uuid();
        } while (static::where('qr_token', $token)->exists());

        return $token;
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

     /**
     * Lookup and search logic for member information 
     */
    public function scopeSearchQuery($query, ?string $searchTerm)
    {
        $term = trim($searchTerm ?? '');
        
        if (empty($term)) {
            return $query;
        }

        $digitsOnly = preg_replace('/\D/', '', $term);

        return $query->where(function ($q) use ($term, $digitsOnly) {
            $q->where('qr_token', $term) 
            ->orWhere('membership_no', $term)
            ->orWhere('id', $term)
            ->orWhere('first_name', 'LIKE', "%{$term}%")
            ->orWhere('last_name', 'LIKE', "%{$term}%")
            
            // Database-safe concatenation
            ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$term}%"])
            
            ->orWhere('membership_no', 'LIKE', "%{$term}%");

            if (!empty($digitsOnly)) {
                $q->orWhere('membership_no', 'LIKE', "%{$digitsOnly}%");
            }
        });
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLogging::class);
    }
}
