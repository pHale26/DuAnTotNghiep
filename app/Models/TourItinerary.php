<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Class TourItinerary
 *
 * @property int $id
 * @property int $tour_id
 * @property int $day_number
 * @property string $title
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Tour $tour
 */
class TourItinerary extends Model
{
    use HasTranslations;

    protected $table = 'tour_itineraries';

    protected $casts = [
        'tour_id' => 'int',
        'day_number' => 'int',
    ];

    public $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function activities()
    {
        return $this->hasMany(TourActivity::class, 'tour_itinerary_id');
    }
}
