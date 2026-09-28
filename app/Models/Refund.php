<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Refund
 *
 * @property int $id
 * @property int $booking_id
 * @property int $payment_id
 * @property float $refund_amount
 * @property string|null $reason
 * @property string|null $status
 * @property Carbon|null $processed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Booking $booking
 * @property Payment $payment
 */
class Refund extends Model
{
    protected $table = 'refunds';

    protected $casts = [
        'booking_id' => 'int',
        'payment_id' => 'int',
        'refund_amount' => 'float',
        'processed_at' => 'datetime',
    ];

    protected $fillable = [
        'booking_id',
        'payment_id',
        'refund_amount',
        'reason',
        'status',
        'processed_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
