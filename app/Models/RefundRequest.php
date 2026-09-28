<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'status',
        'refund_method',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'transaction_reference',
        'processed_by',
        'processed_at',
        'notes',
        'reason',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
