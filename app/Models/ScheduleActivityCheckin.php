<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleActivityCheckin extends Model
{
    protected $table = 'schedule_activity_checkins';

    protected $fillable = [
        'tour_schedule_id',
        'tour_activity_id',
        'guide_id',
        'checked_in_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'tour_schedule_id' => 'int',
        'tour_activity_id' => 'int',
        'guide_id' => 'int',
    ];

    public function tour_schedule()
    {
        return $this->belongsTo(TourSchedule::class);
    }

    public function tour_activity()
    {
        return $this->belongsTo(TourActivity::class);
    }

    public function guide()
    {
        return $this->belongsTo(TourGuide::class, 'guide_id');
    }
}
