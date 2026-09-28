<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ward
 *
 * @property int $id
 * @property int|null $province_id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $type
 * @property string|null $name_with_type
 * @property string|null $path
 * @property string|null $path_with_type
 * @property Province|null $province
 */
class Ward extends Model
{
    protected $table = 'wards';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'province_id' => 'int',
    ];

    protected $fillable = [
        'province_id',
        'name',
        'slug',
        'type',
        'name_with_type',
        'path',
        'path_with_type',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
