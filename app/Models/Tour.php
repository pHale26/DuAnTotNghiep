<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * Class Tour
 */
class Tour extends Model
{
    use HasTranslations, SoftDeletes;

    protected $table = 'tours';

    protected $casts = [
        'destination_id' => 'int',
        'departure_location_id' => 'int',
        'duration_days' => 'int',
        'duration_nights' => 'int',
        'base_price' => 'float',
        'child_price' => 'float',
    ];

    public $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'code',
        'destination_id',
        'departure_location_id',
        'departure_time',
        'meeting_point',
        'title',
        'slug',
        'description',
        'duration_days',
        'duration_nights',
        'base_price',
        'child_price',
        'ai_tags',
        'departure_province_id',
        'departure_ward_id',
        'destination_province_id',
        'destination_ward_id',
        'cost_transport',
        'cost_meal',
        'cost_insurance',
        'cost_service_fee',
    ];

    public function accommodation_tiers()
    {
        return $this->hasMany(TourAccommodationTier::class);
    }

    public function getBasePrice(?Accommodation $accommodation = null, $isHoliday = false)
    {
        $costSum = ($this->cost_transport ?? 0) + ($this->cost_meal ?? 0) + ($this->cost_insurance ?? 0) + ($this->cost_service_fee ?? 0);
        $ticketTotal = $this->tickets->sum('adult_price');
        $calculated = $costSum + $ticketTotal;

        if ($costSum > 0) {
            $base = $calculated;
        } elseif (($this->base_price ?? 0) > 0) {
            $base = (float) $this->base_price;
        } else {
            $base = $calculated;
        }

        if ($accommodation) {
            $base += $isHoliday ? $accommodation->holiday_price_per_adult : $accommodation->price_per_adult;
        }

        return $base;
    }

    /**
     * Giá tour cho trẻ em (dựa trên tỷ lệ child_price_rate trong config).
     */
    public function getChildPrice(): float
    {
        $rate = config('booking.child_price_rate', 0.7);
        $costSum = ($this->cost_transport ?? 0) + ($this->cost_meal ?? 0) + ($this->cost_insurance ?? 0) + ($this->cost_service_fee ?? 0);
        $ticketChildCost = $this->tickets->sum('child_price');

        if ($costSum <= 0 && ($this->base_price ?? 0) > 0) {
            if (($this->child_price ?? 0) > 0) {
                return (float) $this->child_price;
            }
            $ticketAdultCost = $this->tickets->sum('adult_price');
            $costSum = max(0, $this->base_price - $ticketAdultCost);
        }

        return ($costSum * $rate) + $ticketChildCost;
    }

    public function departure_location()
    {
        return $this->belongsTo(Destination::class, 'departure_location_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function departure_province()
    {
        return $this->belongsTo(Province::class, 'departure_province_id');
    }

    public function departure_ward()
    {
        return $this->belongsTo(Ward::class, 'departure_ward_id');
    }

    public function destination_province()
    {
        return $this->belongsTo(Province::class, 'destination_province_id');
    }

    public function destination_ward()
    {
        return $this->belongsTo(Ward::class, 'destination_ward_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tour_id');
    }

    public function tour_categories()
    {
        return $this->hasMany(TourCategory::class, 'tour_id');
    }

    public function tour_images()
    {
        return $this->hasMany(TourImage::class, 'tour_id');
    }

    public function tour_itineraries()
    {
        return $this->hasMany(TourItinerary::class, 'tour_id')
            ->orderBy('day_number', 'asc');
    }

    public function tour_schedules()
    {
        return $this->hasMany(TourSchedule::class, 'tour_id')
            ->orderBy('departure_date', 'asc');
    }

    public function tour_videos()
    {
        return $this->hasMany(TourVideo::class, 'tour_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'tour_id');
    }

    public function getCanBeDeletedAttribute()
    {
        return $this->bookings()->count() === 0;
    }

    public function getRecentBookingsCountAttribute()
    {
        return $this->bookings()
            ->whereIn('status', ['paid', 'confirmed', 'completed'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'tour_categories',
            'tour_id',
            'category_id'
        );
    }

    public function identity()
    {
        return $this->hasOne(UserIdentity::class, 'tour_id');
    }

    public function primaryImage()
    {
        return $this->hasOne(TourImage::class, 'tour_id')
            ->where('is_primary', 1);
    }

    public function activeSchedules()
    {
        return $this->hasMany(TourSchedule::class, 'tour_id')
            ->whereDate('departure_date', '>=', Carbon::today()->addDays(3))
            ->where('status', 'available')
            ->orderBy('departure_date', 'asc');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'tour_tickets', 'tour_id', 'ticket_id');
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'tour_addons', 'tour_id', 'addon_id')
            ->withTimestamps();
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'tour_accommodation_tiers', 'tour_id', 'room_type_id')
            ->join('room_types', 'room_types.id', '=', 'tour_accommodation_tiers.room_type_id')
            ->select('accommodations.*')
            ->distinct();
    }

    protected static function booted()
    {
        static::created(function ($tour) {
            if (empty($tour->code)) {
                $tour->code = self::generateTourCode($tour);
                $tour->save();
            }
        });
    }

    public static function generateTourCode($tour)
    {
        $destName = $tour->destination ? $tour->destination->name : 'NA';
        $destAcronym = self::generateAcronym($destName);

        return sprintf('TR-%s-%dD%dN-%04d', $destAcronym, $tour->duration_days, $tour->duration_nights, $tour->id);
    }

    public static function generateAcronym($string)
    {
        $string = Str::ascii($string);
        $words = explode(' ', $string);
        $acronym = '';
        foreach ($words as $w) {
            if (! empty($w)) {
                $acronym .= strtoupper(substr($w, 0, 1));
            }
        }

        return $acronym;
    }
}
