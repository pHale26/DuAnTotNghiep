<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Concerns\HasLocalImageUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TourImage
 *
 * @property int $id
 * @property int $tour_id
 * @property string $image_url
 * @property bool|null $is_primary
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Tour $tour
 */
class TourImage extends Model
{
    use HasLocalImageUrl;

    protected $table = 'tour_images';

    protected $casts = [
        'tour_id' => 'int',
        'is_primary' => 'bool',
    ];

    protected $fillable = [
        'tour_id',
        'image_url',
        'is_primary',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->resolveImageUrl($value);
    }
}
