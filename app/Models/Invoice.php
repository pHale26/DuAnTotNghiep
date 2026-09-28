<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $booking_id
 * @property int|null $ticket_booking_id
 * @property string|null $invoice_type
 * @property string|null $buyer_name
 * @property string|null $company_name
 * @property string|null $tax_code
 * @property string $billing_address
 * @property string $billing_email
 * @property float $total_amount
 * @property float $tax_amount
 * @property string|null $invoice_number
 * @property string|null $status
 * @property Carbon|null $issued_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Booking|null $booking
 * @property TicketBooking|null $ticket_booking
 */
class Invoice extends Model
{
    protected $table = 'invoices';

    protected $casts = [
        'user_id' => 'int',
        'booking_id' => 'int',
        'ticket_booking_id' => 'int',
        'total_amount' => 'float',
        'tax_amount' => 'float',
        'issued_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'booking_id',
        'ticket_booking_id',
        'invoice_type',
        'buyer_name',
        'company_name',
        'tax_code',
        'billing_address',
        'billing_email',
        'total_amount',
        'tax_amount',
        'invoice_number',
        'status',
        'issued_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticket_booking()
    {
        return $this->belongsTo(TicketBooking::class);
    }
}
