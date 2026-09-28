<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TourCategory
 *
 * @property int $tour_id
 * @property int $category_id
 * @property Tour $tour
 * @property Category $category
 */
class TourCategory extends Model
{
    use SoftDeletes;

    protected $table = 'tour_categories';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'tour_id' => 'int',
        'category_id' => 'int',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
