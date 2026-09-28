<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Destination;
use App\Models\RoomInventory;
use App\Models\RoomType;
use App\Models\Tour;
use App\Models\TourAccommodationTier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed Accommodations (3-4 sao), Room Types, và Room Inventories
 * cho tất cả các điểm đến. Mỗi điểm đến có 1 KS 3 sao + 1 KS 4 sao.
 *
 * Sử dụng firstOrCreate nên chạy lại nhiều lần sẽ không bị trùng.
 */
class AccommodationRoomTypeSeeder extends Seeder
{
    /**
     * Số tháng tạo inventory phía trước.
     */
    private const INVENTORY_MONTHS = 6;

    /**
     * Số phòng mặc định mỗi hạng phòng / ngày.
     */
    private const DEFAULT_TOTAL_ROOMS = 20;

    public function run(): void
    {
        $hotels = $this->getHotelDefinitions();

        foreach ($hotels as $hotelData) {
            $destination = Destination::where('name->vi', $hotelData['destination_name'])
                ->orWhere('name->en', $hotelData['destination_name'])
                ->first();

            if (! $destination) {
                $this->command?->warn("Destination '{$hotelData['destination_name']}' not found, skipping...");

                continue;
            }

            $accommodation = Accommodation::firstOrCreate(
                ['name' => $hotelData['name']],
                [
                    'destination_id' => $destination->id,
                    'address' => $hotelData['address'],
                    'star_rating' => $hotelData['star_rating'],
                    'description' => $hotelData['description'],
                    'is_active' => true,
                ]
            );

            $this->command?->info("Hotel: {$accommodation->name} (dest: {$destination->name})");

            foreach ($hotelData['room_types'] as $rtData) {
                $roomType = RoomType::firstOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'name' => $rtData['name'],
                    ],
                    [
                        'base_capacity' => $rtData['base_capacity'],
                        'max_capacity' => $rtData['max_capacity'],
                        'base_price' => $rtData['base_price'],
                        'extra_bed_price' => $rtData['extra_bed_price'],
                        'child_surcharge_price' => $rtData['child_surcharge_price'] ?? 0,
                    ]
                );

                $this->seedInventory($roomType);

                $this->command?->line("  └ RT: {$roomType->name} ({$rtData['base_price']}đ, cap {$rtData['base_capacity']}/{$rtData['max_capacity']})");
            }
        }

        // Link all multi-day tours to available accommodations
        $this->command?->info('Linking accommodations to multi-day tours...');
        $tours = Tour::where('duration_nights', '>', 0)->get();
        foreach ($tours as $tour) {
            $destId = $tour->destination_id;
            // Get all 3-star and 4-star room types for this destination
            $roomTypes3Star = RoomType::whereHas('accommodation', function ($q) use ($destId) {
                $q->where('destination_id', $destId)->where('star_rating', 3);
            })->get();

            $roomTypes4Star = RoomType::whereHas('accommodation', function ($q) use ($destId) {
                $q->where('destination_id', $destId)->where('star_rating', 4);
            })->get();

            // Assign one 3-star room type as Tiêu chuẩn
            if ($roomTypes3Star->isNotEmpty()) {
                $rt3 = $roomTypes3Star->first();
                TourAccommodationTier::firstOrCreate(
                    ['tour_id' => $tour->id, 'room_type_id' => $rt3->id],
                    ['tier_label' => 'Tiêu chuẩn (3 sao)', 'price_adjustment' => 0, 'is_active' => true]
                );
            }

            // Assign one 4-star room type as Cao cấp
            if ($roomTypes4Star->isNotEmpty()) {
                $rt4 = $roomTypes4Star->first();
                // Price adjustment = Difference in base price
                $diff = $rt4->base_price - ($roomTypes3Star->isNotEmpty() ? $roomTypes3Star->first()->base_price : 0);
                TourAccommodationTier::firstOrCreate(
                    ['tour_id' => $tour->id, 'room_type_id' => $rt4->id],
                    ['tier_label' => 'Nâng cao (4 sao)', 'price_adjustment' => max(0, $diff), 'is_active' => true]
                );
            }
        }

    }

    /**
     * Tạo Room Inventory cho 6 tháng kế tiếp (bỏ qua ngày đã tồn tại).
     */
    private function seedInventory(RoomType $roomType): void
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addMonths(self::INVENTORY_MONTHS);

        // Lấy danh sách ngày đã có inventory
        $existingDates = RoomInventory::where('room_type_id', $roomType->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $inventories = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (in_array($date->toDateString(), $existingDates)) {
                continue;
            }

            $inventories[] = [
                'room_type_id' => $roomType->id,
                'date' => $date->toDateString(),
                'total_rooms' => self::DEFAULT_TOTAL_ROOMS,
                'booked_rooms' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($inventories, 100) as $chunk) {
            DB::table('room_inventories')->insert($chunk);
        }
    }

    /**
     * Danh sách khách sạn + hạng phòng cho tất cả các điểm đến.
     *
     * @return array<int, array{
     *     destination_name: string,
     *     name: string,
     *     address: string,
     *     star_rating: int,
     *     description: string,
     *     room_types: array<int, array{
     *         name: string,
     *         base_capacity: int,
     *         max_capacity: int,
     *         base_price: int,
     *         extra_bed_price: int,
     *         child_surcharge_price?: int
     *     }>
     * }>
     */
    private function getHotelDefinitions(): array
    {
        return [
            // ================================================================
            // ĐÀ NẴNG
            // ================================================================
            [
                'destination_name' => 'Đà Nẵng',
                'name' => 'Gold Hotel Đà Nẵng',
                'address' => '22 Trần Phú, Hải Châu, Đà Nẵng',
                'star_rating' => 3,
                'description' => 'Khách sạn 3 sao nằm trung tâm TP. Đà Nẵng, gần cầu Rồng và chợ Hàn.',
                'room_types' => [
                    ['name' => 'Superior Double', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 450000, 'extra_bed_price' => 150000],
                    ['name' => 'Family Room', 'base_capacity' => 4, 'max_capacity' => 5, 'base_price' => 800000, 'extra_bed_price' => 150000],
                ],
            ],
            [
                'destination_name' => 'Đà Nẵng',
                'name' => 'Muong Thanh Grand Đà Nẵng',
                'address' => '962 Ngô Quyền, Sơn Trà, Đà Nẵng',
                'star_rating' => 4,
                'description' => 'Khách sạn 4 sao thuộc chuỗi Mường Thanh, view sông Hàn, hồ bơi tầng thượng.',
                'room_types' => [
                    ['name' => 'Standard Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 700000, 'extra_bed_price' => 300000],
                    ['name' => 'Superior River View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 900000, 'extra_bed_price' => 350000],
                ],
            ],

            // ================================================================
            // PHÚ QUỐC
            // ================================================================
            [
                'destination_name' => 'Phú Quốc',
                'name' => 'Amon Hotel Phú Quốc',
                'address' => '68 Trần Hưng Đạo, Dương Đông, Phú Quốc, Kiên Giang',
                'star_rating' => 3,
                'description' => 'Khách sạn 3 sao gần chợ đêm Phú Quốc, tiện di chuyển.',
                'room_types' => [
                    ['name' => 'Standard Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 600000, 'extra_bed_price' => 200000],
                    ['name' => 'Deluxe Pool View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 850000, 'extra_bed_price' => 250000],
                    ['name' => 'Family Room', 'base_capacity' => 4, 'max_capacity' => 5, 'base_price' => 1000000, 'extra_bed_price' => 250000],
                ],
            ],
            [
                'destination_name' => 'Phú Quốc',
                'name' => 'Sea Star Resort Phú Quốc',
                'address' => 'Bãi Trường, Dương Tơ, Phú Quốc, Kiên Giang',
                'star_rating' => 4,
                'description' => 'Resort 4 sao nằm sát biển Bãi Trường, phòng rộng rãi, hồ bơi vô cực.',
                'room_types' => [
                    ['name' => 'Superior Garden View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 850000, 'extra_bed_price' => 250000],
                    ['name' => 'Deluxe Sea View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 1100000, 'extra_bed_price' => 300000],
                    ['name' => 'Family Bungalow', 'base_capacity' => 4, 'max_capacity' => 6, 'base_price' => 1500000, 'extra_bed_price' => 300000],
                ],
            ],

            // ================================================================
            // SAPA
            // ================================================================
            [
                'destination_name' => 'Sapa',
                'name' => 'Sapa Charm Hotel',
                'address' => '06 Thạch Sơn, Thị trấn Sa Pa, Lào Cai',
                'star_rating' => 3,
                'description' => 'Khách sạn 3 sao ngay trung tâm thị trấn Sapa, view thung lũng Mường Hoa.',
                'room_types' => [
                    ['name' => 'Standard Double', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 500000, 'extra_bed_price' => 150000],
                    ['name' => 'Superior Valley View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 700000, 'extra_bed_price' => 200000],
                ],
            ],
            [
                'destination_name' => 'Sapa',
                'name' => 'Sapa Legend Hotel',
                'address' => '12 Mường Hoa, Thị trấn Sa Pa, Lào Cai',
                'star_rating' => 4,
                'description' => 'Khách sạn 4 sao view ruộng bậc thang, tiêu chuẩn dịch vụ cao cấp.',
                'room_types' => [
                    ['name' => 'Standard Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 800000, 'extra_bed_price' => 300000],
                    ['name' => 'Suite Mountain View', 'base_capacity' => 2, 'max_capacity' => 4, 'base_price' => 1400000, 'extra_bed_price' => 400000],
                ],
            ],

            // ================================================================
            // HÀ NỘI
            // ================================================================
            [
                'destination_name' => 'Hà Nội',
                'name' => 'Hà Nội Pearl Hotel',
                'address' => '06 Bảo Khánh, Hoàn Kiếm, Hà Nội',
                'star_rating' => 3,
                'description' => 'Khách sạn 3 sao nằm ngay phố cổ, cách Hồ Hoàn Kiếm 200m.',
                'room_types' => [
                    ['name' => 'Standard Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 550000, 'extra_bed_price' => 200000],
                    ['name' => 'Superior City View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 750000, 'extra_bed_price' => 250000],
                    ['name' => 'Family Room', 'base_capacity' => 4, 'max_capacity' => 5, 'base_price' => 950000, 'extra_bed_price' => 200000],
                ],
            ],
            [
                'destination_name' => 'Hà Nội',
                'name' => 'La Siesta Premium Hà Nội',
                'address' => '32 Hàng Bè, Hoàn Kiếm, Hà Nội',
                'star_rating' => 4,
                'description' => 'Khách sạn boutique 4 sao sang trọng giữa trung tâm phố cổ Hà Nội.',
                'room_types' => [
                    ['name' => 'Deluxe Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 900000, 'extra_bed_price' => 350000],
                    ['name' => 'Premium Suite', 'base_capacity' => 2, 'max_capacity' => 4, 'base_price' => 1300000, 'extra_bed_price' => 400000],
                ],
            ],

            // ================================================================
            // ĐÀ LẠT
            // ================================================================
            [
                'destination_name' => 'Đà Lạt',
                'name' => 'Dalat De Charme Hotel',
                'address' => '09 Hồ Tùng Mậu, Phường 3, TP. Đà Lạt, Lâm Đồng',
                'star_rating' => 3,
                'description' => 'Khách sạn 3 sao kiến trúc Pháp cổ điển, nằm gần chợ Đà Lạt.',
                'room_types' => [
                    ['name' => 'Standard Double', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 500000, 'extra_bed_price' => 150000],
                    ['name' => 'Superior Balcony', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 700000, 'extra_bed_price' => 200000],
                    ['name' => 'Family Room', 'base_capacity' => 4, 'max_capacity' => 5, 'base_price' => 900000, 'extra_bed_price' => 200000],
                ],
            ],
            [
                'destination_name' => 'Đà Lạt',
                'name' => 'Terracotta Hotel & Resort Đà Lạt',
                'address' => '19 Hoa Hồng, Phường 9, TP. Đà Lạt, Lâm Đồng',
                'star_rating' => 4,
                'description' => 'Resort 4 sao nằm trên đồi thông, không gian yên tĩnh, view đồi núi.',
                'room_types' => [
                    ['name' => 'Superior Garden View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 850000, 'extra_bed_price' => 300000],
                    ['name' => 'Deluxe Lake View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 1100000, 'extra_bed_price' => 350000],
                ],
            ],

            // ================================================================
            // HẠ LONG
            // ================================================================
            [
                'destination_name' => 'Hạ Long',
                'name' => 'Hạ Long Dream Hotel',
                'address' => '08 Hạ Long, Bãi Cháy, TP. Hạ Long, Quảng Ninh',
                'star_rating' => 3,
                'description' => 'Khách sạn 3 sao view vịnh Hạ Long, gần bến tàu du lịch.',
                'room_types' => [
                    ['name' => 'Standard Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 500000, 'extra_bed_price' => 150000],
                    ['name' => 'Superior Bay View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 750000, 'extra_bed_price' => 200000],
                ],
            ],
            [
                'destination_name' => 'Hạ Long',
                'name' => 'Novotel Hạ Long Bay',
                'address' => '160 Hạ Long, Bãi Cháy, TP. Hạ Long, Quảng Ninh',
                'star_rating' => 4,
                'description' => 'Khách sạn 4 sao quốc tế với view toàn cảnh vịnh Hạ Long, hồ bơi ngoài trời.',
                'room_types' => [
                    ['name' => 'Superior Room', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 900000, 'extra_bed_price' => 300000],
                    ['name' => 'Deluxe Bay View', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 1200000, 'extra_bed_price' => 350000],
                    ['name' => 'Family Suite', 'base_capacity' => 4, 'max_capacity' => 6, 'base_price' => 1800000, 'extra_bed_price' => 400000],
                ],
            ],
        ];
    }
}
