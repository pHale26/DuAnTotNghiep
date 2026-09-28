<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAccommodation extends Model
{
    protected $fillable = [
        'booking_id',
        'room_type_id',
        'room_type_name_snapshot',
        'accommodation_name_snapshot',
        'price_snapshot',
        'extra_bed_price_snapshot',
        'child_surcharge_snapshot',
        'num_adults',
        'num_children',
        'extra_bed_qty',
        'single_rooms_count',
        'child_surcharge_total',
        'extra_bed_total',
        'total_amount',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
}
