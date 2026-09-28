<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Concerns\HasLocalImageUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketImage
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $image_url
 * @property bool|null $is_primary
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Ticket $ticket
 */
class TicketImage extends Model
{
    use HasLocalImageUrl;

    protected $table = 'ticket_images';

    protected $casts = [
        'ticket_id' => 'int',
        'is_primary' => 'bool',
    ];

    protected $fillable = [
        'ticket_id',
        'image_url',
        'is_primary',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->resolveImageUrl($value);
    }
}
