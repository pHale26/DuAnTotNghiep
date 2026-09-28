<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TourVideo
 *
 * @property int $id
 * @property int $tour_id
 * @property string $video_url
 * @property string|null $thumbnail_url
 * @property string|null $platform
 * @property int|null $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Tour $tour
 */
class TourVideo extends Model
{
    protected $table = 'tour_videos';

    protected $casts = [
        'tour_id' => 'int',
        'sort_order' => 'int',
    ];

    protected $fillable = [
        'tour_id',
        'video_url',
        'thumbnail_url',
        'platform',
        'sort_order',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
