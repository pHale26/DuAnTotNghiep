<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accommodation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'destination_id',
        'name',
        'address',
        'description',
        'star_rating',
        'is_active',
    ];

    protected $casts = [
        'star_rating' => 'integer',
        'is_active' => 'boolean',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function room_types()
    {
        return $this->hasMany(RoomType::class);
    }

    public function getTourIdsAttribute()
    {
        return TourAccommodationTier::whereIn(
            'room_type_id',
            $this->room_types()->pluck('id')
        )->pluck('tour_id')->unique()->toArray();
    }

    public function syncTours(array $tourIds)
    {
        $roomTypeIds = $this->room_types()->pluck('id')->toArray();

        if (empty($roomTypeIds)) {
            return;
        }

        // Delete tiers for this accommodation's room types that are not in the new tour list
        TourAccommodationTier::whereIn('room_type_id', $roomTypeIds)
            ->whereNotIn('tour_id', $tourIds)
            ->delete();

        // Add tiers for new tours
        foreach ($tourIds as $tourId) {
            foreach ($roomTypeIds as $roomTypeId) {
                TourAccommodationTier::firstOrCreate([
                    'tour_id' => $tourId,
                    'room_type_id' => $roomTypeId,
                ], [
                    'tier_label' => 'Tiêu chuẩn',
                    'price_adjustment' => 0,
                    'is_active' => true,
                ]);
            }
        }
    }
}
