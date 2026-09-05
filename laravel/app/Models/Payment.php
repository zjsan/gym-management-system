<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'receipt_no',
        'member_id',
        'walkin_id',
        'processed_by',
        'category',
        'amount',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function walkin()
    {
        return $this->belongsTo(Walkin::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    

      /**
     *
     * Generates a unique, human-readable reference number for every 
     * cash transaction formatted by date and sequential count
     */
    public static function generateReceiptNumber(): string
    {
        $dateStr = now()->format('Ymd');
        $count = Payment::whereDate('created_at', now()->today())->count() + 1;
        return 'PAY-' . $dateStr . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
