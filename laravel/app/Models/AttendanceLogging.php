<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLogging extends Model
{
    protected $fillable = [
        'recorded_by',
        'member_id',
        'walkin_id',
        'entry_method',
        'check_in'
    ];

    protected $casts = [
        'check_in' => 'datetime',
    ];

    // Staff member tracking
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Member tracking
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // Walkin tracking
    public function walkin(): BelongsTo
    {
        return $this->belongsTo(Walkin::class);
    }
}
