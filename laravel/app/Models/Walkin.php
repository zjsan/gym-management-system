<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AttendanceLogging;

class Walkin extends Model
{
    protected $fillable = ['name', 'walkin_fee'];

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLogging::class);
    }
}