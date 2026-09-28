<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TourGuide
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $bio
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|ScheduleGuide[] $schedule_guides
 */
class TourGuide extends Model
{
    protected $table = 'tour_guides';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'bio',
        'guide_card_type',
        'languages',
        'is_blacklisted',
        'kpi_score',
        'status',
    ];

    protected $casts = [
        'languages' => 'array',
        'is_blacklisted' => 'boolean',
        'kpi_score' => 'decimal:1',
    ];

    public function schedule_guides()
    {
        return $this->hasMany(ScheduleGuide::class, 'guide_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'guide_id');
    }

    public function updateKpiScore()
    {
        $avgRating = Review::where('guide_id', $this->id)
            ->whereNotNull('guide_rating')
            ->avg('guide_rating');

        $this->update(['kpi_score' => $avgRating ? round($avgRating, 1) : 0]);
    }
}
