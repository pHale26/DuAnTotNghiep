<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingPassenger
 *
 * @property int $id
 * @property int $booking_id
 * @property string $full_name
 * @property Carbon|null $date_of_birth
 * @property string|null $identity_number
 * @property string|null $gender
 * @property string|null $passenger_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Booking $booking
 */
class BookingPassenger extends Model
{
    protected $table = 'booking_passengers';

    protected $casts = [
        'booking_id' => 'int',
        'date_of_birth' => 'datetime',
        'checked_in' => 'boolean',
        'is_free_time' => 'boolean',
        'free_time_start' => 'datetime',
        'free_time_end' => 'datetime',
    ];

    protected $fillable = [
        'booking_id',
        'full_name',
        'date_of_birth',
        'identity_number',
        'gender',
        'passenger_type',
        'checked_in',
        'special_note',
        'is_free_time',
        'free_time_start',
        'free_time_end',
        'free_time_location',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function activity_checkins()
    {
        return $this->hasMany(ActivityPassengerCheckin::class);
    }

    public function group_splits()
    {
        return $this->hasMany(GroupSplit::class, 'guest_id');
    }
}
