<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourAccommodationTier extends Model
{
    protected $fillable = [
        'tour_id',
        'room_type_id',
        'tier_label',
        'price_adjustment',
        'is_active',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
}
