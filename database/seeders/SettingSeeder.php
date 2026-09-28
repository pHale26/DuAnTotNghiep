<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set(
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS',
            '24',
            'Ngưỡng thời gian (giờ) để phân loại mức độ khẩn cấp của yêu cầu báo bận tour. Mặc định là 24 giờ.'
        );
    }
}
