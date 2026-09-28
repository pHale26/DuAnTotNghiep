<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wishlist
 *
 * @property int $user_id
 * @property int $tour_id
 * @property Carbon $created_at
 * @property User $user
 * @property Tour $tour
 */
class Wishlist extends Model
{
    protected $table = 'wishlists';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tour_id',
        'created_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'tour_id' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
