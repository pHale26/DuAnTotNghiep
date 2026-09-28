<?php

namespace App\Models;

use App\Models\Concerns\HasLocalImageUrl;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TourActivity extends Model
{
    use HasLocalImageUrl, HasTranslations;

    protected $table = 'tour_activities';

    public $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'tour_itinerary_id',
        'activity_type',
        'start_time',
        'end_time',
        'title',
        'description',
        'image_url',
    ];

    public function tour_itinerary()
    {
        return $this->belongsTo(TourItinerary::class, 'tour_itinerary_id');
    }

    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->resolveImageUrl($value);
    }

    public function getActivityTypeLabelAttribute(): string
    {
        $labels = [
            'Transportation' => 'Di chuyển',
            'Attractions' => 'Điểm tham quan',
            'Accommodation' => 'Lưu trú',
            'Dining' => 'Ẩm thực',
            'Entertainment' => 'Giải trí',
            'Shopping' => 'Mua sắm',
            'Others' => 'Khác',
        ];

        return $labels[$this->activity_type] ?? $this->activity_type ?? 'Khác';
    }
}
