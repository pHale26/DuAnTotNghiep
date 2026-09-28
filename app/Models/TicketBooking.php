<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketBooking
 *
 * @property int $id
 * @property int $user_id
 * @property int $ticket_option_id
 * @property int $quantity
 * @property float $total_price
 * @property float|null $discount_amount
 * @property int|null $coupon_id
 * @property Carbon $visit_date
 * @property string|null $booking_status
 * @property string|null $qr_code_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property TicketOption $ticket_option
 * @property Coupon|null $coupon
 * @property Collection|Invoice[] $invoices
 * @property Collection|Payment[] $payments
 */
class TicketBooking extends Model
{
    protected $table = 'ticket_bookings';

    protected $casts = [
        'user_id' => 'int',
        'ticket_option_id' => 'int',
        'quantity' => 'int',
        'total_price' => 'float',
        'discount_amount' => 'float',
        'coupon_id' => 'int',
        'visit_date' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'ticket_option_id',
        'quantity',
        'total_price',
        'discount_amount',
        'coupon_id',
        'visit_date',
        'booking_status',
        'qr_code_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket_option()
    {
        return $this->belongsTo(TicketOption::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
