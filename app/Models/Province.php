<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Province
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_slug
 * @property string|null $full_name
 * @property string|null $type
 * @property Collection|Ward[] $wards
 */
class Province extends Model
{
    protected $table = 'provinces';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'name',
        'name_slug',
        'full_name',
        'type',
    ];

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}
