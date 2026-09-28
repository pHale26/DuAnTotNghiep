<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingAddon
 *
 * @property int $id
 * @property int $booking_id
 * @property int|null $addon_id
 * @property string $addon_name
 * @property float $price
 * @property int|null $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Booking $booking
 * @property Addon|null $addon
 */
class BookingAddon extends Model
{
    protected $table = 'booking_addons';

    protected $casts = [
        'booking_id' => 'int',
        'addon_id' => 'int',
        'price' => 'float',
        'quantity' => 'int',
    ];

    protected $fillable = [
        'booking_id',
        'addon_id',
        'addon_name',
        'price',
        'quantity',
        'usage_date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}
