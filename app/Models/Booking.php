<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking
 *
 * @property int $id
 * @property int $user_id
 * @property int $tour_schedule_id
 * @property int|null $coupon_id
 * @property float $total_price
 * @property float|null $discount_amount
 * @property int $adults_count
 * @property int $children_count
 * @property string|null $booking_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property TourSchedule $tour_schedule
 * @property Coupon|null $coupon
 * @property Collection|Addon[] $addons
 * @property Collection|BookingPassenger[] $booking_passengers
 * @property Collection|Invoice[] $invoices
 * @property Collection|Payment[] $payments
 * @property Collection|Refund[] $refunds
 */
class Booking extends Model
{
    protected $table = 'bookings';

    protected $casts = [
        'user_id' => 'int',
        'tour_schedule_id' => 'int',
        'coupon_id' => 'int',
        'total_price' => 'float',
        'discount_amount' => 'float',
        'adults_count' => 'int',
        'children_count' => 'int',
        'transport_price' => 'float',
        'transport_data' => 'array',
    ];

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_PAID_30 = 'paid_30';

    public const PAYMENT_PAID_100 = 'paid_100';

    public const PAYMENT_FAILED = 'failed';

    public const TOUR_UPCOMING = 'upcoming';

    public const TOUR_IN_PROGRESS = 'in_progress';

    public const TOUR_CHECKING_IN = 'checking_in';

    public const TOUR_COMPLETED = 'completed';

    public const TOUR_CANCELLED_CUSTOMER = 'cancelled_by_customer';

    public const TOUR_CANCELLED_ADMIN = 'cancelled_by_admin';

    protected $fillable = [
        'user_id',
        'tour_schedule_id',
        'coupon_id',
        'total_price',
        'discount_amount',
        'adults_count',
        'children_count',
        'payment_status',
        'tour_status',
        'booking_status',
        'current_checkin_step',
        'pnr_code',
        'transport_type',
        'transport_price',
        'transport_data',
        'payment_method',
        'is_passenger_list_submitted',
        'payment_type',
        'paid_amount',
        'payment_step',
        'meeting_point',
        'cancel_reason',
        'invoice_status',
        'invoice_email',
        'invoice_requested_at',
        'invoice_sent_at',
        'accommodation_id',
        'single_rooms_count',
        'extra_beds_count',
        'price_breakdown',
    ];

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class); // Legacy
    }

    public function booking_accommodations()
    {
        return $this->hasMany(BookingAccommodation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour_schedule()
    {
        return $this->belongsTo(TourSchedule::class);
    }

    public function tour()
    {
        return $this->hasOneThrough(
            Tour::class,
            TourSchedule::class,
            'id', // Foreign key on tour_schedules table
            'id', // Foreign key on tours table
            'tour_schedule_id', // Local key on bookings table
            'tour_id' // Local key on tour_schedules table
        );
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'booking_addons')
            ->withPivot('id', 'addon_name', 'price', 'quantity')
            ->withTimestamps();
    }

    public function refund_request()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function booking_passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function ticket_bookings()
    {
        return $this->hasMany(TicketBooking::class);
    }

    /**
     * Tự động cập nhật các booking sắp khởi hành (upcoming) sang đang diễn ra (in_progress)
     * nếu thời gian hiện tại đã vượt qua ngày và giờ khởi hành của tour.
     */
    public static function updateUpcomingTourStatuses(): void
    {
        $now = now();
        $todayDate = $now->toDateString();

        $bookings = self::where('tour_status', self::TOUR_UPCOMING)
            ->whereHas('tour_schedule', function ($q) use ($todayDate) {
                $q->whereDate('departure_date', '<=', $todayDate)
                    ->whereDate('return_date', '>=', $todayDate);
            })
            ->with(['tour_schedule.tour'])
            ->get();

        foreach ($bookings as $booking) {
            $schedule = $booking->tour_schedule;
            if (! $schedule) {
                continue;
            }

            $tour = $schedule->tour;
            $departureDateTime = Carbon::parse($schedule->departure_date->toDateString());

            if ($tour && $tour->departure_time) {
                $timeParts = explode(':', $tour->departure_time);
                $hour = isset($timeParts[0]) ? (int) $timeParts[0] : 0;
                $minute = isset($timeParts[1]) ? (int) $timeParts[1] : 0;
                $second = isset($timeParts[2]) ? (int) $timeParts[2] : 0;
                $departureDateTime->setTime($hour, $minute, $second);
            }

            if ($now->greaterThanOrEqualTo($departureDateTime)) {
                $booking->tour_status = self::TOUR_IN_PROGRESS;
                $booking->save();
            }
        }
    }

    /**
     * Lấy danh sách các trạng thái tour tiếp theo hợp lệ từ trạng thái hiện tại.
     * Tránh việc thay đổi trạng thái tour nhảy cóc.
     *
     * @return array<int, string>
     */
    public static function getValidNextStatuses(string $currentStatus): array
    {
        return match ($currentStatus) {
            self::TOUR_UPCOMING => [
                self::TOUR_UPCOMING,
                self::TOUR_IN_PROGRESS,
                self::TOUR_CHECKING_IN,
                self::TOUR_COMPLETED,
                self::TOUR_CANCELLED_ADMIN,
            ],
            self::TOUR_IN_PROGRESS => [
                self::TOUR_IN_PROGRESS,
                self::TOUR_CHECKING_IN,
                self::TOUR_COMPLETED,
                self::TOUR_CANCELLED_ADMIN,
            ],
            self::TOUR_CHECKING_IN => [
                self::TOUR_CHECKING_IN,
                self::TOUR_IN_PROGRESS,
                self::TOUR_COMPLETED,
                self::TOUR_CANCELLED_ADMIN,
            ],
            default => [
                $currentStatus,
            ],
        };
    }

    public function getCodeAttribute()
    {
        return 'BK-'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Số tiền đặt cọc (dựa trên tỷ lệ cọc trong config).
     */
    public function getDepositAmountAttribute(): float
    {
        return $this->total_price * config('booking.deposit_rate');
    }

    /**
     * Số tiền còn lại chưa thanh toán.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_price - ($this->paid_amount ?? 0));
    }

    /**
     * Phần trăm đã thanh toán (0–100).
     */
    public function getPaidPercentAttribute(): int
    {
        if ($this->total_price <= 0) {
            return 0;
        }

        return min(100, (int) round(($this->paid_amount ?? 0) / $this->total_price * 100));
    }

    /**
     * Tổng tiền dịch vụ bổ sung (addons) đã snapshot.
     */
    public function getAddonTotalAttribute(): float
    {
        return $this->addons->sum(fn ($a) => ($a->pivot->price ?? 0) * ($a->pivot->quantity ?? 0));
    }

    protected function casts(): array
    {
        return [
            'invoice_requested_at' => 'datetime',
            'invoice_sent_at' => 'datetime',
            'price_breakdown' => 'array',
        ];
    }
}
