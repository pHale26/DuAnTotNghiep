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
use Spatie\Translatable\HasTranslations;

/**
 * Class Destination
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Collection|Ticket[] $tickets
 * @property Collection|Tour[] $tours
 */
class Destination extends Model
{
    use HasLocalImageUrl, HasTranslations, SoftDeletes;

    protected $table = 'destinations';

    protected $fillable = [
        'name',
        'description',
        'image_url',
    ];

    public $translatable = [
        'name',
        'description',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class);
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->resolveImageUrl($value);
    }
}
