<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property string|null $role
 * @property string|null $preferences
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|Booking[] $bookings
 * @property Collection|Invoice[] $invoices
 * @property Collection|Review[] $reviews
 * @property Collection|TicketBooking[] $ticket_bookings
 * @property Collection|UserAddress[] $user_addresses
 * @property UserIdentity|null $user_identity
 * @property Collection|Wishlist[] $wishlists
 */
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $table = 'users';

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'preferences',
        'google_id',
        'google_avatar',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ticket_bookings()
    {
        return $this->hasMany(TicketBooking::class);
    }

    public function user_addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function user_identity()
    {
        return $this->hasOne(UserIdentity::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function identity()
    {
        return $this->hasOne(UserIdentity::class);
    }

    public function tour_guide()
    {
        return $this->hasOne(TourGuide::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
