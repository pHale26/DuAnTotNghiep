<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Review
 *
 * @property int $id
 * @property int $user_id
 * @property int $tour_id
 * @property int $rating
 * @property string|null $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Tour $tour
 */
class Review extends Model
{
    protected $table = 'reviews';

    protected $casts = [
        'user_id' => 'int',
        'tour_id' => 'int',
        'rating' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'tour_id',
        'rating',
        'comment',
        'guide_id',
        'guide_rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function tour_guide()
    {
        return $this->belongsTo(TourGuide::class, 'guide_id');
    }
}
