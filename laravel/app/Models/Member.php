<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //
    protected $fillable = [
        'membersip_no', 'first_name', 'last_name', 'contact_number', 
        'address', 'gender', 'photo_path', 'is_active', 
        'membership_start', 'membership_end'
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
