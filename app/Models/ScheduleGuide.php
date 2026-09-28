<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ScheduleGuide
 *
 * @property int $tour_schedule_id
 * @property int $guide_id
 * @property TourSchedule $tour_schedule
 * @property TourGuide $tour_guide
 */
class ScheduleGuide extends Model
{
    protected $table = 'schedule_guides';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tour_schedule_id',
        'guide_id',
        'is_backup',
    ];

    protected $casts = [
        'tour_schedule_id' => 'int',
        'guide_id' => 'int',
        'is_backup' => 'bool',
    ];

    public function tour_schedule()
    {
        return $this->belongsTo(TourSchedule::class);
    }

    public function tour_guide()
    {
        return $this->belongsTo(TourGuide::class, 'guide_id');
    }
}
