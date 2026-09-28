<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'accommodation_id',
        'name',
        'base_capacity',
        'max_capacity',
        'base_price',
        'extra_bed_price',
        'child_surcharge_price',
        'total_rooms',
    ];

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function inventories()
    {
        return $this->hasMany(RoomInventory::class);
    }

    public function booking_accommodations()
    {
        return $this->hasMany(BookingAccommodation::class);
    }
}
