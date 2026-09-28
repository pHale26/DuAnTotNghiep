<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TourSchedule
 *
 * @property int $id
 * @property int $tour_id
 * @property Carbon $departure_date
 * @property Carbon $return_date
 * @property int $capacity
 * @property int $available_seats
 * @property string|null $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Tour $tour
 * @property Collection|Booking[] $bookings
 * @property Collection|ScheduleGuide[] $schedule_guides
 */
class TourSchedule extends Model
{
    protected $table = 'tour_schedules';

    protected $casts = [
        'tour_id' => 'int',
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
        'capacity' => 'int',
        'available_seats' => 'int',
        'reminder_sent' => 'boolean',
    ];

    protected $fillable = [
        'tour_id',
        'departure_date',
        'return_date',
        'capacity',
        'available_seats',
        'status',
        'checkin_location',
        'reminder_sent',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function schedule_guides()
    {
        return $this->hasMany(ScheduleGuide::class);
    }

    public function activity_checkins()
    {
        return $this->hasMany(ScheduleActivityCheckin::class);
    }

    public function getCodeAttribute()
    {
        if ($this->tour) {
            return $this->tour->code.'-'.Carbon::parse($this->departure_date)->format('dmy');
        }

        return '';
    }
}
