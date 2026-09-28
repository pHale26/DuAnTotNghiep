<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomInventory extends Model
{
    protected $fillable = [
        'room_type_id',
        'date',
        'total_rooms',
        'booked_rooms',
    ];

    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function getAvailableRoomsAttribute()
    {
        return max(0, $this->total_rooms - $this->booked_rooms);
    }
}
