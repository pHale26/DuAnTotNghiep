<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Concerns\HasLocalImageUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Addon
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property string|null $image_url
 * @property bool|null $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Collection|Booking[] $bookings
 */
class Addon extends Model
{
    use HasLocalImageUrl, SoftDeletes;

    protected $table = 'addons';

    protected $casts = [
        'price' => 'float',
        'is_active' => 'bool',
    ];

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'image_url',
        'is_active',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_addons')
            ->withPivot('id', 'addon_name', 'price', 'quantity', 'usage_date')
            ->withTimestamps();
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_addons')
            ->withTimestamps();
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->resolveImageUrl($value);
    }
}
