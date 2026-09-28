<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAssignmentLog extends Model
{
    protected $fillable = [
        'tour_schedule_id',
        'user_id',
        'action',
        'description',
        'details',
    ];

    /**
     * Define the casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    /**
     * Relationship with Tour Schedule.
     */
    public function tour_schedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class);
    }

    /**
     * Relationship with User (actor who logged the change).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
