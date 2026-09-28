<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'phone' => '0987654321',
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Nhân viên',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
                'phone' => '0912345678',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Khách hàng',
                'password' => Hash::make('12345678'),
                'role' => 'customer',
            ]
        );

        // 2. Seed Banners
        $heroBanners = [
            [
                'title' => 'Hành trình khám phá Việt Nam',
                'image_url' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=1600',
                'position' => 'hero',
                'sort_order' => 1,
            ],
            [
                'title' => 'Nghỉ dưỡng đẳng cấp tại Phú Quốc',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1600',
                'position' => 'hero',
                'sort_order' => 2,
            ],
            [
                'title' => 'Chinh phục đỉnh Fansipan - Sapa',
                'image_url' => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=1600',
                'position' => 'hero',
                'sort_order' => 3,
            ],
        ];

        foreach ($heroBanners as $banner) {
            Banner::firstOrCreate(
                ['title' => $banner['title']],
                [
                    'image_url' => $banner['image_url'],
                    'position' => $banner['position'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => 1,
                ]
            );
        }

        $adBanners = [
            [
                'title' => 'Giảm giá 30% khi đặt sớm',
                'image_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=600',
                'target_url' => '/tour-tron-goi?budget=under_5m',
                'position' => 'home_ads',
                'sort_order' => 1,
            ],
            [
                'title' => 'Combo vé máy bay & khách sạn',
                'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=600',
                'target_url' => '/flights',
                'position' => 'home_ads',
                'sort_order' => 2,
            ],
            [
                'title' => 'Khám phá ẩm thực miền Trung',
                'image_url' => 'https://images.unsplash.com/photo-1563897539633-7374c276c212?q=80&w=600',
                'target_url' => '/tour-tron-goi',
                'position' => 'home_ads',
                'sort_order' => 3,
            ],
        ];

        foreach ($adBanners as $banner) {
            Banner::firstOrCreate(
                ['title' => $banner['title']],
                [
                    'image_url' => $banner['image_url'],
                    'target_url' => $banner['target_url'],
                    'position' => $banner['position'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => 1,
                ]
            );
        }

        // 3. Seed Categories
        $categoriesData = [
            ['name' => 'Du lịch biển', 'slug' => 'du-lich-bien'],
            ['name' => 'Khám phá di sản', 'slug' => 'kham-pha-di-san'],
            ['name' => 'Nghỉ dưỡng núi', 'slug' => 'nghi-duong-nui'],
            ['name' => 'Tour phiêu lưu', 'slug' => 'tour-phieu-luu'],
            ['name' => 'Du lịch gia đình', 'slug' => 'du-lich-gia-dinh'],
        ];

        foreach ($categoriesData as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']]
            );
        }

        // 4. Seed Roles & Permissions (phải chạy sau khi users đã được tạo)
        $this->call([
            RolePermissionSeeder::class,
        ]);

        if (class_exists(CouponSeeder::class)) {
            // 5. Seed Coupons
            $this->call([
                CouponSeeder::class,
            ]);
        }

        // 6. Master Tour Seeder (Destinations, Tours, Itineraries, Tickets, Addons)
        $this->call([
            MasterTourSeeder::class,
        ]);

        // 7. Demo Seeder (Dữ liệu cố định cho demo ngày 21/07/2026)
        $this->call([
            DemoSeeder::class,
        ]);

        // 8. Vietnam Holiday Seeder
        $this->call([
            VietnamHolidaySeeder::class,
        ]);

        // 9. Accommodation + Room Types + Inventory (kiến trúc 3 lớp mới)
        $this->call([
            AccommodationRoomTypeSeeder::class,
        ]);

        // 10. Dashboard Seeder (100 đơn hàng mẫu và dữ liệu thống kê)
        $this->call([
            DashboardSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
