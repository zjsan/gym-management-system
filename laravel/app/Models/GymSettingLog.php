<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GymSettingLog extends Model
{
    //
    use HasFactory;

    // Turn off updated_at since logs are immutable
    public $timestamps = false;

    protected $fillable = [
        'gym_setting_id',
        'key',
        'old_value',
        'new_value',
        'updated_by',
        'created_at',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
