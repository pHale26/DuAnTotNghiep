<?php

namespace App\Services;

use App\Events\SeatAvailabilityUpdated;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\BookingAccommodation;
use App\Models\BookingAddon;
use App\Models\BookingPassenger;
use App\Models\Coupon;
use App\Models\Holiday;
use App\Models\RoomInventory;
use App\Models\RoomType;
use App\Models\TicketBooking;
use App\Models\TicketOption;
use App\Models\TourSchedule;
use App\Models\User;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourBookingService
{
    /**
     * Create a new tour booking
     *
     * @throws Exception
     */
    public function createBooking(array $data, ?User $user, string $sessionId): Booking
    {
        if ($user && ! empty($data['customer_phone'])) {
            $user->phone = $data['customer_phone'];
            $user->save();
        }

        return DB::transaction(function () use ($data, $user, $sessionId) {
            $totalPersons = $data['adults'] + $data['children'];
            $schedule = TourSchedule::with('tour')->lockForUpdate()->find($data['schedule_id']);

            if (! $schedule || $schedule->status !== 'available' || Carbon::parse($schedule->departure_date)->lt(Carbon::today()->addDays(3))) {
                throw new Exception('Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');
            }

            if ($schedule->available_seats < $totalPersons) {
                throw new Exception('Tour không còn đủ chỗ trống cho số lượng hành khách này. Vui lòng chọn ngày khác.');
            }

            if ($user) {
                $this->handleUserIdentity($data, $user);
            }

            $pricing = $this->calculatePricing($schedule, $data);

            $transportData = null;
            if (! empty($data['transport_data'])) {
                $transportData = is_string($data['transport_data']) ? json_decode($data['transport_data'], true) : $data['transport_data'];
            }

            $booking = new Booking;
            $booking->user_id = $user?->id;
            $booking->customer_name = $data['customer_name'] ?? ($user?->name);
            $booking->customer_email = $data['customer_email'] ?? ($user?->email);
            $booking->customer_phone = $data['customer_phone'] ?? ($user?->phone);
            $booking->tour_schedule_id = $data['schedule_id'];
            $booking->adults_count = $data['adults'];
            $booking->children_count = $data['children'];
            $booking->total_price = $pricing['finalTotalPrice'];
            $booking->discount_amount = $pricing['discountAmount'];
            $booking->coupon_id = $pricing['couponId'];
            $booking->payment_status = Booking::PAYMENT_PENDING;
            $booking->tour_status = Booking::TOUR_UPCOMING;
            $booking->transport_type = $data['transport_type'];
            $booking->transport_price = $data['transport_price'] ?? 0;
            $booking->transport_data = $transportData;
            $booking->meeting_point = $data['meeting_point']
                ?? $schedule->tour->meeting_point;

            $booking->payment_type = $data['payment_type'] ?? 'full';
            $booking->payment_method = $data['payment_method'] ?? 'transfer';
            $booking->paid_amount = 0;
            $booking->is_passenger_list_submitted = ($totalPersons < 2);
            $booking->accommodation_id = null; // Legacy
            $booking->single_rooms_count = $data['single_rooms_count'] ?? 1;
            $booking->extra_beds_count = $data['extra_beds_count'] ?? 0;
            $booking->price_breakdown = $pricing['priceBreakdown'] ?? null;
            $booking->save();

            if (! empty($data['accommodation_id'])) {
                $room = RoomType::with('accommodation')->find($data['accommodation_id']);
                if ($room) {
                    BookingAccommodation::create([
                        'booking_id' => $booking->id,
                        'room_type_id' => $room->id,
                        'room_type_name_snapshot' => $room->name,
                        'accommodation_name_snapshot' => $room->accommodation->name ?? '',
                        'price_snapshot' => $room->base_price,
                        'extra_bed_price_snapshot' => $room->extra_bed_price,
                        'child_surcharge_snapshot' => $room->child_surcharge_price,
                        'num_adults' => $data['adults'],
                        'num_children' => $data['children'],
                        'single_rooms_count' => $data['single_rooms_count'] ?? 1,
                        'extra_bed_qty' => $data['extra_beds_count'] ?? 0,
                        'child_surcharge_total' => $pricing['priceBreakdown']['accommodation_child'] ?? 0,
                        'extra_bed_total' => $pricing['priceBreakdown']['accommodation_extra_bed'] ?? 0,
                        'total_amount' => ($pricing['priceBreakdown']['accommodation_base'] ?? 0)
                                        + ($pricing['priceBreakdown']['accommodation_extra_bed'] ?? 0)
                                        + ($pricing['priceBreakdown']['accommodation_child'] ?? 0),
                    ]);

                    // Deduct inventory
                    $inventory = RoomInventory::where('room_type_id', $room->id)
                        ->where('date', $schedule->departure_date->toDateString())
                        ->first();
                    if ($inventory) {
                        $inventory->increment('booked_rooms', $data['single_rooms_count'] ?? 1);
                    } else {
                        // Create inventory record if not exists (assume unlimited or handle later)
                        RoomInventory::create([
                            'room_type_id' => $room->id,
                            'date' => $schedule->departure_date->toDateString(),
                            'total_rooms' => 10, // default fallback
                            'booked_rooms' => $data['single_rooms_count'] ?? 1,
                        ]);
                    }
                }
            }

            $this->saveTicketBookings($pricing['selectedTickets'], $booking, $user, $schedule->departure_date);
            $this->saveBookingAddons($pricing['selectedAddons'], $booking);
            $this->saveBookingPassengers($data, $booking);

            $schedule->available_seats -= $totalPersons;
            $schedule->save();

            $this->releaseSeatHold($schedule->id, $user->id ?? $sessionId);

            broadcast(new SeatAvailabilityUpdated($schedule->id, $schedule->available_seats))->toOthers();

            return $booking;
        });
    }

    private function handleUserIdentity(array $data, User $user): void
    {
        $identity = UserIdentity::where('user_id', $user->id)->first();

        if (! $identity) {
            $identity = new UserIdentity;
            $identity->user_id = $user->id;
        } else {
            $primaryIdentityNumber = $data['passengers']['adult'][0]['identity_number'] ?? null;
            if ($primaryIdentityNumber) {
                $existingIdentity = UserIdentity::where('identity_number_hash', hash('sha256', $primaryIdentityNumber))
                    ->where('user_id', '!=', $user->id)
                    ->first();

                if ($existingIdentity) {
                    throw new Exception('Số CCCD/Hộ chiếu này đã được đăng ký bởi người dùng khác. Vui lòng kiểm tra lại.');
                }
            }
        }

        $primaryAdult = $data['passengers']['adult'][0] ?? null;
        if ($primaryAdult) {
            $identity->full_name = $primaryAdult['full_name'];
            $identity->identity_number = $primaryAdult['identity_number'] ?? null;
            $identity->date_of_birth = $primaryAdult['date_of_birth'];
            $identity->gender = $primaryAdult['gender'];
            $identity->issue_date = $data['issue_date'] ?? '2020-01-01';
            $identity->expiry_date = $data['expiry_date'] ?? '2040-01-01';
            $identity->issue_place = $data['issue_place'] ?? 'Hà Nội';

            if (isset($data['front_image']) && $data['front_image'] instanceof UploadedFile) {
                $frontContent = file_get_contents($data['front_image']->getRealPath());
                $encryptedFront = Crypt::encrypt($frontContent);
                $frontFilename = Str::uuid().'.enc';
                Storage::disk('local')->put('private/identities/'.$frontFilename, $encryptedFront);
                $identity->front_image_url = 'private/identities/'.$frontFilename;
            }

            if (isset($data['back_image']) && $data['back_image'] instanceof UploadedFile) {
                $backContent = file_get_contents($data['back_image']->getRealPath());
                $encryptedBack = Crypt::encrypt($backContent);
                $backFilename = Str::uuid().'.enc';
                Storage::disk('local')->put('private/identities/'.$backFilename, $encryptedBack);
                $identity->back_image_url = 'private/identities/'.$backFilename;
            }

            $identity->save();
        }
    }

    private function calculatePricing(TourSchedule $schedule, array $data): array
    {
        $holidaySurcharge = Holiday::getIncreasePercentage($schedule->departure_date);
        $isHoliday = $holidaySurcharge > 0;

        $tour = $schedule->tour;
        $costTransport = $tour->cost_transport ?? 0;
        $costMeal = $tour->cost_meal ?? 0;
        $costInsurance = $tour->cost_insurance ?? 0;
        $costServiceFee = $tour->cost_service_fee ?? 0;

        $ticketAdultCost = 0;
        $ticketChildCost = 0;
        foreach ($tour->tickets as $ticket) {
            $ticketAdultCost += $ticket->adult_price ?? 0;
            $ticketChildCost += $ticket->child_price ?? 0;
        }

        $baseCostSum = $costTransport + $costMeal + $costInsurance + $costServiceFee;
        if ($baseCostSum <= 0 && ($tour->base_price ?? 0) > 0) {
            $baseCostSum = max(0, $tour->base_price - $ticketAdultCost);
        }

        $childRate = config('booking.child_price_rate', 0.7);
        $tourBasePerAdult = $baseCostSum + $ticketAdultCost;
        $tourBasePerChild = ($baseCostSum * $childRate) + $ticketChildCost;

        if ($isHoliday) {
            $tourBasePerAdult = $tourBasePerAdult * (1 + $holidaySurcharge / 100);
            $tourBasePerChild = $tourBasePerChild * (1 + $holidaySurcharge / 100);
        }

        // 2. Accommodation Cost
        $roomId = $data['accommodation_id'] ?? null;
        $roomsCount = $data['single_rooms_count'] ?? 1;
        $extraBedsCount = $data['extra_beds_count'] ?? 0;

        $accBase = 0;
        $accExtra = 0;
        $accChild = 0;
        $selectedRoom = null;
        $totalAccCost = 0;

        if ($roomId) {
            $selectedRoom = RoomType::find($roomId);
            if ($selectedRoom) {
                // RoomType doesn't have built-in holiday prices in our schema, but we can apply the generic holiday surcharge percentage
                $base = $selectedRoom->base_price;
                $extra = $selectedRoom->extra_bed_price;
                $child = $selectedRoom->child_surcharge_price;

                if ($isHoliday) {
                    $base = $base * (1 + $holidaySurcharge / 100);
                    $extra = $extra * (1 + $holidaySurcharge / 100);
                    $child = $child * (1 + $holidaySurcharge / 100);
                }

                $accBase = $base;
                $accExtra = $extra;
                $accChild = $child;
            }
        }

        $totalAccCost = ($accBase * $roomsCount)
                      + ($accExtra * $extraBedsCount)
                      + ($accChild * $data['children']);

        // 3. Combine to get Calculated Price
        $calculatedPrice = ($tourBasePerAdult * $data['adults']) + ($tourBasePerChild * $data['children']) + $totalAccCost;

        $transportPrice = $data['transport_price'] ?? 0;

        // Tickets (Optional extras)
        $ticketPrice = 0;
        $selectedTickets = [];
        if (! empty($data['tickets']) && is_array($data['tickets'])) {
            foreach ($data['tickets'] as $ticketOptionId => $qty) {
                if ($qty > 0) {
                    $opt = TicketOption::find($ticketOptionId);
                    if ($opt) {
                        $ticketPrice += $opt->price * $qty;
                        $selectedTickets[] = [
                            'option' => $opt,
                            'qty' => $qty,
                        ];
                    }
                }
            }
        }

        // Addons (Optional extras)
        $addonPriceTotal = 0;
        $selectedAddons = [];
        if (! empty($data['addons']) && is_array($data['addons'])) {
            foreach ($data['addons'] as $addonId => $addonData) {
                $qty = isset($addonData['qty']) ? (int) $addonData['qty'] : 0;
                if ($qty > 0) {
                    $addon = Addon::find($addonId);
                    if ($addon) {
                        $usageDate = $addonData['usage_date'] ?? $schedule->departure_date;
                        $addonSurcharge = Holiday::getIncreasePercentage($usageDate);
                        $price = $addon->price * (1 + $addonSurcharge / 100);

                        $addonPriceTotal += $price * $qty;
                        $selectedAddons[] = [
                            'addon_id' => $addon->id,
                            'addon_name' => $addon->name,
                            'price' => $price,
                            'quantity' => $qty,
                            'usage_date' => $usageDate,
                        ];
                    }
                }
            }
        }

        $finalTotalPrice = $calculatedPrice + $transportPrice + $ticketPrice + $addonPriceTotal;
        $discountAmount = 0;
        $couponId = null;

        if (! empty($data['coupon_code'])) {
            $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();
            $coupon = Coupon::where('code', $data['coupon_code'])
                ->where(function ($query) {
                    $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                })
                ->where(function ($query) use ($tourCategoryIds) {
                    $query->whereNull('category_id')
                        ->orWhereIn('category_id', $tourCategoryIds);
                })
                ->first();

            if ($coupon && $finalTotalPrice >= $coupon->min_order_value) {
                if ($coupon->usage_limit === null || $coupon->used_count < $coupon->usage_limit) {
                    $discount = 0;
                    if ($coupon->discount_type === 'percent') {
                        $discount = $finalTotalPrice * ($coupon->discount_value / 100);
                        if ($coupon->max_discount) {
                            $discount = min($discount, $coupon->max_discount);
                        }
                    } else {
                        $discount = $coupon->discount_value;
                    }
                    $discountAmount = $discount;
                    $couponId = $coupon->id;
                    $finalTotalPrice = max(0, $finalTotalPrice - $discountAmount);

                    $coupon->increment('used_count');
                }
            }
        }

        $calcTransport = $costTransport * $data['adults'] + ($costTransport * $childRate * $data['children']);
        $calcMeal = $costMeal * $data['adults'] + ($costMeal * $childRate * $data['children']);
        $calcInsurance = $costInsurance * $data['adults'] + ($costInsurance * $childRate * $data['children']);
        $calcService = $costServiceFee * $data['adults'] + ($costServiceFee * $childRate * $data['children']);
        $calcTicket = $ticketAdultCost * $data['adults'] + $ticketChildCost * $data['children'];

        if ($isHoliday) {
            $calcTransport *= (1 + $holidaySurcharge / 100);
            $calcMeal *= (1 + $holidaySurcharge / 100);
            $calcInsurance *= (1 + $holidaySurcharge / 100);
            $calcService *= (1 + $holidaySurcharge / 100);
            $calcTicket *= (1 + $holidaySurcharge / 100);
        }

        $priceBreakdown = [
            'transport' => $calcTransport,
            'meal' => $calcMeal,
            'insurance' => $calcInsurance,
            'service_fee' => $calcService,
            'accommodation_base' => $accBase * $roomsCount,
            'accommodation_single_supplement' => 0, // Legacy
            'accommodation_extra_bed' => $accExtra * $extraBedsCount,
            'accommodation_child' => $accChild * $data['children'],
            'ticket' => $calcTicket,
        ];

        return [
            'finalTotalPrice' => $finalTotalPrice,
            'discountAmount' => $discountAmount,
            'couponId' => $couponId,
            'selectedTickets' => $selectedTickets,
            'selectedAddons' => $selectedAddons,
            'priceBreakdown' => $priceBreakdown,
        ];
    }

    private function saveTicketBookings(array $selectedTickets, Booking $booking, ?User $user, string $departureDate): void
    {
        foreach ($selectedTickets as $item) {
            $tb = new TicketBooking;
            $tb->user_id = $user?->id;
            $tb->booking_id = $booking->id;
            $tb->ticket_option_id = $item['option']->id;
            $tb->quantity = $item['qty'];
            $tb->total_price = $item['option']->price * $item['qty'];
            $tb->visit_date = $departureDate;
            $tb->booking_status = 'pending';
            $tb->save();
        }
    }

    private function saveBookingAddons(array $selectedAddons, Booking $booking): void
    {
        foreach ($selectedAddons as $item) {
            BookingAddon::create([
                'booking_id' => $booking->id,
                'addon_id' => $item['addon_id'],
                'addon_name' => $item['addon_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'usage_date' => $item['usage_date'],
            ]);
        }
    }

    private function saveBookingPassengers(array $data, Booking $booking): void
    {
        if (isset($data['passengers']['adult'])) {
            foreach ($data['passengers']['adult'] as $adult) {
                $passenger = new BookingPassenger;
                $passenger->booking_id = $booking->id;
                $passenger->full_name = $adult['full_name'];
                $passenger->date_of_birth = $adult['date_of_birth'];
                $passenger->identity_number = $adult['identity_number'] ?? null;
                $passenger->gender = $adult['gender'];
                $passenger->passenger_type = 'adult';
                $passenger->save();
            }
        }

        if (isset($data['passengers']['child'])) {
            foreach ($data['passengers']['child'] as $child) {
                $passenger = new BookingPassenger;
                $passenger->booking_id = $booking->id;
                $passenger->full_name = $child['full_name'];
                $passenger->date_of_birth = $child['date_of_birth'];
                $passenger->gender = $child['gender'];
                $passenger->passenger_type = 'child';
                $passenger->save();
            }
        }
    }

    private function releaseSeatHold(int $scheduleId, $identifier): void
    {
        $holdKey = "tour_schedule_{$scheduleId}_holds";
        $currentHolds = Cache::get($holdKey, []);
        if (isset($currentHolds[$identifier])) {
            unset($currentHolds[$identifier]);
            if (empty($currentHolds)) {
                Cache::forget($holdKey);
            } else {
                $maxExpiresAt = max(array_column($currentHolds, 'expires_at'));
                Cache::put($holdKey, $currentHolds, Carbon::createFromTimestamp($maxExpiresAt));
            }
        }
    }
}
