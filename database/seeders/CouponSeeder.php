<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 0,
                'max_discount' => 500000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'usage_limit' => 100,
                'used_count' => 0,
            ],
            [
                'code' => 'SUMMER200K',
                'discount_type' => 'fixed',
                'discount_value' => 200000,
                'min_order_value' => 2000000,
                'max_discount' => 200000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(1),
                'usage_limit' => 50,
                'used_count' => 0,
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
