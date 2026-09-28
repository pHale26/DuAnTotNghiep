<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityPassengerCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_schedule_id',
        'tour_activity_id',
        'booking_passenger_id',
    ];

    public function tour_schedule()
    {
        return $this->belongsTo(TourSchedule::class);
    }

    public function tour_activity()
    {
        return $this->belongsTo(TourActivity::class);
    }

    public function booking_passenger()
    {
        return $this->belongsTo(BookingPassenger::class);
    }
}
