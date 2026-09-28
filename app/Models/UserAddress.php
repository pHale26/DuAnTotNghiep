<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAddress
 *
 * @property int $id
 * @property int $user_id
 * @property bool|null $is_default
 * @property string|null $address_type
 * @property string $receiver_name
 * @property string $phone
 * @property string $province_id
 * @property string $ward_id
 * @property string $detailed_address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
class UserAddress extends Model
{
    protected $table = 'user_addresses';

    protected $casts = [
        'user_id' => 'int',
        'is_default' => 'bool',
    ];

    protected $fillable = [
        'user_id',
        'is_default',
        'address_type',
        'receiver_name',
        'phone',
        'province_id',
        'ward_id',
        'detailed_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
