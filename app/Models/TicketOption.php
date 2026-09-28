<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketOption
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property float|null $original_price
 * @property string|null $conditions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Ticket $ticket
 * @property Collection|TicketBooking[] $ticket_bookings
 */
class TicketOption extends Model
{
    protected $table = 'ticket_options';

    protected $casts = [
        'ticket_id' => 'int',
        'price' => 'float',
        'original_price' => 'float',
        'conditions' => 'json',
    ];

    protected $fillable = [
        'ticket_id',
        'name',
        'description',
        'price',
        'original_price',
        'conditions',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function ticket_bookings()
    {
        return $this->hasMany(TicketBooking::class);
    }
}
