<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 *
 * @property int $id
 * @property int|null $booking_id
 * @property int|null $ticket_booking_id
 * @property float $amount
 * @property string $payment_method
 * @property string|null $transaction_code
 * @property string|null $payment_status
 * @property Carbon|null $paid_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Booking|null $booking
 * @property TicketBooking|null $ticket_booking
 * @property Collection|Refund[] $refunds
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $casts = [
        'booking_id' => 'int',
        'ticket_booking_id' => 'int',
        'amount' => 'float',
        'paid_at' => 'datetime',
    ];

    protected $fillable = [
        'booking_id',
        'ticket_booking_id',
        'amount',
        'payment_method',
        'transaction_code',
        'payment_status',
        'paid_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticket_booking()
    {
        return $this->belongsTo(TicketBooking::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
