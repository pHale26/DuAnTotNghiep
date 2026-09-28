<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'stop_id',
        'guest_id',
        'guest_name',
        'reason',
        'phone_number',
        'start_time',
        'end_time',
        'return_location',
        'split_location',
        'status',
        'split_started_at',
        'returned_at',
        'created_by',
        'cancel_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'split_started_at' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_ON_TIME = 'ON_TIME';

    const STATUS_OVERDUE = 'OVERDUE';

    const STATUS_UNREACHABLE = 'UNREACHABLE';

    const STATUS_RETURNED = 'RETURNED';

    const STATUS_CANCELLED = 'CANCELLED';

    public function guest()
    {
        return $this->belongsTo(BookingPassenger::class, 'guest_id');
    }

    public function extensions()
    {
        return $this->hasMany(GroupSplitExtension::class, 'group_split_id')->orderBy('created_at', 'desc');
    }
}
