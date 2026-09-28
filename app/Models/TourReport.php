<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourReport extends Model
{
    protected $fillable = [
        'tour_schedule_id',
        'guide_id',
        'actual_guests',
        'incident_notes',
        'advance_amount',
        'actual_expense',
        'balance',
        'status',
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
