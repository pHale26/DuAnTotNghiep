<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAbsenceRequest extends Model
{
    protected $fillable = [
        'tour_id',
        'tour_schedule_id',
        'main_guide_id',
        'reason',
        'attachment_url',
        'status',
        'urgency_level',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
        'new_main_guide_id',
        'new_backup_guide_id',
    ];

    /**
     * Define the casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Relationship with the Tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Relationship with the Tour Schedule.
     */
    public function tour_schedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class);
    }

    /**
     * Relationship with the requesting primary Tour Guide.
     */
    public function main_guide(): BelongsTo
    {
        return $this->belongsTo(TourGuide::class, 'main_guide_id');
    }

    /**
     * Relationship with the replacement primary Tour Guide.
     */
    public function new_main_guide(): BelongsTo
    {
        return $this->belongsTo(TourGuide::class, 'new_main_guide_id');
    }

    /**
     * Relationship with the new backup Tour Guide.
     */
    public function new_backup_guide(): BelongsTo
    {
        return $this->belongsTo(TourGuide::class, 'new_backup_guide_id');
    }

    /**
     * Relationship with the admin User who reviewed the request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
