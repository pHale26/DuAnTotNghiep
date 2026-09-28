<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;

    protected $table = 'holidays';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price_increase_percentage' => 'float',
    ];

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'price_increase_percentage',
    ];

    /**
     * Check if a given date falls in any holiday.
     *
     * @param  string|Carbon  $date
     * @return bool
     */
    public static function isHoliday($date)
    {
        try {
            $dateStr = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            $dateStr = is_string($date) ? explode(' ', $date)[0] : $date;
        }

        return self::where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr)
            ->exists();
    }

    /**
     * Get the max price increase percentage for a given date.
     *
     * @param  string|Carbon  $date
     * @return float
     */
    public static function getIncreasePercentage($date)
    {
        try {
            $dateStr = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            $dateStr = is_string($date) ? explode(' ', $date)[0] : $date;
        }

        $maxIncrease = self::where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr)
            ->max('price_increase_percentage');

        return (float) ($maxIncrease ?? 0);
    }
}
