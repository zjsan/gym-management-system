<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //
    protected $fillable = [
        'membership_no', 'first_name', 'last_name', 'contact_number', 'emergency_contact_number',
        'address', 'gender', 'date_of_birth', 'age', 'photo_path', 'is_active',
        'membership_start', 'membership_end'
    ];

    protected $casts = [
        'membership_start' => 'datetime',
        'membership_end' => 'datetime',
        'last_renewal_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Check if membership is expired (based on 30-day rule)
    public function isExpired()
    {
        return now()->greaterThan($this->membership_end);
    }

    // Logic to renew: always adds 30 days
    public function renew()
    {
        $this->membership_start = now();
        $this->membership_end = now()->addDays(30);
        $this->save();
    }

    //function to adjust membership end date by adding 1 day
    public function adjust_membership()
    {
        $this->membership_end = now()->addDays(1);//increase 1 day to the current end date
        $this->save();
    }
}
