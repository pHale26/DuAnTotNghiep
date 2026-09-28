<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            'Đà Nẵng' => [
                ['name' => 'Khách sạn 3 sao', 'address' => '123 Võ Nguyên Giáp, Phước Mỹ, Sơn Trà, Đà Nẵng (Gần biển)'],
                ['name' => 'Khách sạn 4 sao', 'address' => '270 Võ Nguyên Giáp, Bắc Mỹ Phú, Ngũ Hành Sơn, Đà Nẵng (View biển)'],
                ['name' => 'Resort 5 sao', 'address' => '105 Võ Nguyên Giáp, Khuê Mỹ, Ngũ Hành Sơn, Đà Nẵng (Bãi biển riêng)'],
            ],
            'Phú Quốc' => [
                ['name' => 'Khách sạn 3 sao', 'address' => '68 Trần Hưng Đạo, Dương Đông, Phú Quốc, Kiên Giang (Trung tâm)'],
                ['name' => 'Khách sạn 4 sao', 'address' => '100 Trần Hưng Đạo, Dương Đông, Phú Quốc, Kiên Giang (Sát biển)'],
                ['name' => 'Resort 5 sao', 'address' => 'Bãi Dài, Gành Dầu, Phú Quốc, Kiên Giang (Khu bãi biển riêng)'],
            ],
            'Sapa' => [
                ['name' => 'Khách sạn 3 sao', 'address' => '24 Phan Xi Păng, Phường Sapa, TX Sapa, Lào Cai (Gần nhà thờ)'],
                ['name' => 'Khách sạn 4 sao', 'address' => '01 Hoàng Liên, Phường Sapa, TX Sapa, Lào Cai (View Thung lũng)'],
                ['name' => 'Resort 5 sao', 'address' => 'Đường Mường Hoa, Thị xã Sapa, Lào Cai (Gần Bản Cát Cát)'],
            ],
            'Hà Nội' => [
                ['name' => 'Khách sạn 3 sao', 'address' => '45 Hàng Bạc, Hoàn Kiếm, Hà Nội (Khu Phố Cổ)'],
                ['name' => 'Khách sạn 4 sao', 'address' => '12 Lý Thái Tổ, Hoàn Kiếm, Hà Nội (Gần Hồ Gươm)'],
                ['name' => 'Khách sạn 5 sao', 'address' => '15 Ngô Quyền, Hoàn Kiếm, Hà Nội (Trung tâm)'],
            ],
            'Đà Lạt' => [
                ['name' => 'Khách sạn 3 sao', 'address' => '12 Bùi Thị Xuân, Phường 2, Đà Lạt, Lâm Đồng (Gần Chợ)'],
                ['name' => 'Khách sạn 4 sao', 'address' => '03 Nguyễn Thái Học, Phường 1, Đà Lạt, Lâm Đồng (View Hồ Xuân Hương)'],
                ['name' => 'Resort 5 sao', 'address' => 'Đường Trần Hưng Đạo, Phường 10, Đà Lạt, Lâm Đồng (Khu biệt thự cổ)'],
            ],
            'Hạ Long' => [
                ['name' => 'Khách sạn 3 sao', 'address' => 'Đường Hạ Long, Phường Bãi Cháy, TP Hạ Long, Quảng Ninh'],
                ['name' => 'Khách sạn 4 sao', 'address' => 'Khu du lịch Bãi Cháy, TP Hạ Long, Quảng Ninh (View Vịnh)'],
                ['name' => 'Resort 5 sao', 'address' => 'Đảo Rều, Phường Bãi Cháy, TP Hạ Long, Quảng Ninh (Đảo riêng)'],
            ],
        ];

        $prices = [
            'Khách sạn 3 sao' => ['price_per_adult' => 400000, 'price_single_supplement' => 300000, 'price_extra_bed' => 250000, 'price_child' => 200000],
            'Khách sạn 4 sao' => ['price_per_adult' => 800000, 'price_single_supplement' => 600000, 'price_extra_bed' => 500000, 'price_child' => 400000],
            'Khách sạn 5 sao' => ['price_per_adult' => 1500000, 'price_single_supplement' => 1200000, 'price_extra_bed' => 1000000, 'price_child' => 750000],
            'Resort 5 sao' => ['price_per_adult' => 1500000, 'price_single_supplement' => 1200000, 'price_extra_bed' => 1000000, 'price_child' => 750000],
        ];

        foreach ($destinations as $destName => $accList) {
            foreach ($accList as $accInfo) {
                $p = $prices[$accInfo['name']];
                Accommodation::firstOrCreate([
                    'name' => $accInfo['name'].' - '.$destName,
                ], [
                    'address' => $accInfo['address'],
                    'description' => 'Dịch vụ lưu trú tiêu chuẩn, tiện nghi đầy đủ, vị trí vô cùng thuận lợi.',
                    'price_per_adult' => $p['price_per_adult'],
                    'price_single_supplement' => $p['price_single_supplement'],
                    'price_extra_bed' => $p['price_extra_bed'],
                    'price_child' => $p['price_child'],
                    'holiday_price_per_adult' => $p['price_per_adult'] * 1.5,
                    'holiday_price_single_supplement' => $p['price_single_supplement'] * 1.5,
                    'holiday_price_extra_bed' => $p['price_extra_bed'] * 1.5,
                    'holiday_price_child' => $p['price_child'] * 1.5,
                    'is_active' => true,
                ]);
            }
        }
    }
}
