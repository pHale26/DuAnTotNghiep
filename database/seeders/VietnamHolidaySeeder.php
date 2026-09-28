<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

/**
 * Seeder cho các ngày lễ, tết và sự kiện quốc gia Việt Nam.
 *
 * Bao gồm:
 *  - Tết Dương lịch
 *  - Tết Nguyên Đán (Âm lịch, ngày dương lịch tương ứng 2025-2027)
 *  - Ngày Thống nhất, Quốc tế Lao động, Quốc khánh
 *  - Giỗ Tổ Hùng Vương
 *  - Ngày 8/3, 20/10, 20/11, 1/6, v.v.
 */
class VietnamHolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            // ============================================================
            // TẾT DƯƠNG LỊCH (1/1 hàng năm)
            // ============================================================
            ['name' => 'Tết Dương lịch', 'start_date' => '2025-01-01', 'end_date' => '2025-01-01', 'price_increase_percentage' => 15],
            ['name' => 'Tết Dương lịch', 'start_date' => '2026-01-01', 'end_date' => '2026-01-01', 'price_increase_percentage' => 15],
            ['name' => 'Tết Dương lịch', 'start_date' => '2027-01-01', 'end_date' => '2027-01-01', 'price_increase_percentage' => 15],

            // ============================================================
            // TẾT NGUYÊN ĐÁN - 2025 (27/1 - 2/2 dương lịch)
            // ============================================================
            ['name' => 'Tết Nguyên Đán 2025', 'start_date' => '2025-01-27', 'end_date' => '2025-02-02', 'price_increase_percentage' => 40],

            // ============================================================
            // TẾT NGUYÊN ĐÁN - 2026 (16/2 - 22/2 dương lịch)
            // ============================================================
            ['name' => 'Tết Nguyên Đán 2026', 'start_date' => '2026-02-16', 'end_date' => '2026-02-22', 'price_increase_percentage' => 40],

            // ============================================================
            // TẾT NGUYÊN ĐÁN - 2027 (5/2 - 11/2 dương lịch)
            // ============================================================
            ['name' => 'Tết Nguyên Đán 2027', 'start_date' => '2027-02-05', 'end_date' => '2027-02-11', 'price_increase_percentage' => 40],

            // ============================================================
            // GIỖ TỔ HÙNG VƯƠNG (10/3 âm lịch)
            // ============================================================
            ['name' => 'Giỗ Tổ Hùng Vương', 'start_date' => '2025-04-07', 'end_date' => '2025-04-07', 'price_increase_percentage' => 20],
            ['name' => 'Giỗ Tổ Hùng Vương', 'start_date' => '2026-04-26', 'end_date' => '2026-04-26', 'price_increase_percentage' => 20],
            ['name' => 'Giỗ Tổ Hùng Vương', 'start_date' => '2027-04-16', 'end_date' => '2027-04-16', 'price_increase_percentage' => 20],

            // ============================================================
            // NGÀY GIẢI PHÓNG MIỀN NAM / THỐNG NHẤT ĐẤT NƯỚC (30/4)
            // ============================================================
            ['name' => 'Ngày Giải phóng miền Nam 30/4', 'start_date' => '2025-04-30', 'end_date' => '2025-04-30', 'price_increase_percentage' => 25],
            ['name' => 'Ngày Giải phóng miền Nam 30/4', 'start_date' => '2026-04-30', 'end_date' => '2026-04-30', 'price_increase_percentage' => 25],
            ['name' => 'Ngày Giải phóng miền Nam 30/4', 'start_date' => '2027-04-30', 'end_date' => '2027-04-30', 'price_increase_percentage' => 25],

            // ============================================================
            // NGÀY QUỐC TẾ LAO ĐỘNG (1/5)
            // ============================================================
            ['name' => 'Ngày Quốc tế Lao động 1/5', 'start_date' => '2025-05-01', 'end_date' => '2025-05-01', 'price_increase_percentage' => 25],
            ['name' => 'Ngày Quốc tế Lao động 1/5', 'start_date' => '2026-05-01', 'end_date' => '2026-05-01', 'price_increase_percentage' => 25],
            ['name' => 'Ngày Quốc tế Lao động 1/5', 'start_date' => '2027-05-01', 'end_date' => '2027-05-01', 'price_increase_percentage' => 25],

            // Nghỉ lễ 30/4 - 1/5 ghép (thường nhà nước cho nghỉ dài)
            ['name' => 'Lễ 30/4 - 1/5', 'start_date' => '2026-04-30', 'end_date' => '2026-05-03', 'price_increase_percentage' => 30],

            // ============================================================
            // NGÀY QUỐC KHÁNH (2/9)
            // ============================================================
            ['name' => 'Quốc khánh 2/9', 'start_date' => '2025-09-02', 'end_date' => '2025-09-02', 'price_increase_percentage' => 25],
            ['name' => 'Quốc khánh 2/9', 'start_date' => '2026-09-02', 'end_date' => '2026-09-02', 'price_increase_percentage' => 25],
            ['name' => 'Quốc khánh 2/9', 'start_date' => '2027-09-02', 'end_date' => '2027-09-02', 'price_increase_percentage' => 25],

            // ============================================================
            // CÁC NGÀY KỶ NIỆM VÀ SỰ KIỆN QUAN TRỌNG
            // ============================================================
            // Cách mạng tháng 8 (19/8)
            ['name' => 'Cách mạng tháng 8 (19/8)', 'start_date' => '2025-08-19', 'end_date' => '2025-08-19', 'price_increase_percentage' => 0],
            ['name' => 'Cách mạng tháng 8 (19/8)', 'start_date' => '2026-08-19', 'end_date' => '2026-08-19', 'price_increase_percentage' => 0],
            ['name' => 'Cách mạng tháng 8 (19/8)', 'start_date' => '2027-08-19', 'end_date' => '2027-08-19', 'price_increase_percentage' => 0],

            // Ngày Phụ nữ Quốc tế (8/3)
            ['name' => 'Ngày Phụ nữ Quốc tế 8/3', 'start_date' => '2025-03-08', 'end_date' => '2025-03-08', 'price_increase_percentage' => 10],
            ['name' => 'Ngày Phụ nữ Quốc tế 8/3', 'start_date' => '2026-03-08', 'end_date' => '2026-03-08', 'price_increase_percentage' => 10],
            ['name' => 'Ngày Phụ nữ Quốc tế 8/3', 'start_date' => '2027-03-08', 'end_date' => '2027-03-08', 'price_increase_percentage' => 10],

            // Ngày Quốc tế Thiếu nhi (1/6)
            ['name' => 'Ngày Thiếu nhi 1/6', 'start_date' => '2025-06-01', 'end_date' => '2025-06-01', 'price_increase_percentage' => 10],
            ['name' => 'Ngày Thiếu nhi 1/6', 'start_date' => '2026-06-01', 'end_date' => '2026-06-01', 'price_increase_percentage' => 10],
            ['name' => 'Ngày Thiếu nhi 1/6', 'start_date' => '2027-06-01', 'end_date' => '2027-06-01', 'price_increase_percentage' => 10],

            // Ngày Phụ nữ Việt Nam (20/10)
            ['name' => 'Ngày Phụ nữ Việt Nam 20/10', 'start_date' => '2025-10-20', 'end_date' => '2025-10-20', 'price_increase_percentage' => 10],
            ['name' => 'Ngày Phụ nữ Việt Nam 20/10', 'start_date' => '2026-10-20', 'end_date' => '2026-10-20', 'price_increase_percentage' => 10],
            ['name' => 'Ngày Phụ nữ Việt Nam 20/10', 'start_date' => '2027-10-20', 'end_date' => '2027-10-20', 'price_increase_percentage' => 10],

            // Ngày Nhà giáo Việt Nam (20/11)
            ['name' => 'Ngày Nhà giáo 20/11', 'start_date' => '2025-11-20', 'end_date' => '2025-11-20', 'price_increase_percentage' => 5],
            ['name' => 'Ngày Nhà giáo 20/11', 'start_date' => '2026-11-20', 'end_date' => '2026-11-20', 'price_increase_percentage' => 5],
            ['name' => 'Ngày Nhà giáo 20/11', 'start_date' => '2027-11-20', 'end_date' => '2027-11-20', 'price_increase_percentage' => 5],

            // Ngày Thương binh Liệt sĩ (27/7)
            ['name' => 'Ngày Thương binh Liệt sĩ 27/7', 'start_date' => '2025-07-27', 'end_date' => '2025-07-27', 'price_increase_percentage' => 0],
            ['name' => 'Ngày Thương binh Liệt sĩ 27/7', 'start_date' => '2026-07-27', 'end_date' => '2026-07-27', 'price_increase_percentage' => 0],
            ['name' => 'Ngày Thương binh Liệt sĩ 27/7', 'start_date' => '2027-07-27', 'end_date' => '2027-07-27', 'price_increase_percentage' => 0],

            // Ngày Quân đội Nhân dân (22/12)
            ['name' => 'Ngày Quân đội Nhân dân 22/12', 'start_date' => '2025-12-22', 'end_date' => '2025-12-22', 'price_increase_percentage' => 0],
            ['name' => 'Ngày Quân đội Nhân dân 22/12', 'start_date' => '2026-12-22', 'end_date' => '2026-12-22', 'price_increase_percentage' => 0],

            // ============================================================
            // TẾT TRUNG THU (15/8 âm lịch)
            // ============================================================
            ['name' => 'Tết Trung Thu', 'start_date' => '2025-10-06', 'end_date' => '2025-10-06', 'price_increase_percentage' => 15],
            ['name' => 'Tết Trung Thu', 'start_date' => '2026-09-25', 'end_date' => '2026-09-25', 'price_increase_percentage' => 15],

            // ============================================================
            // LỄ GIÁNG SINH (25/12)
            // ============================================================
            ['name' => 'Lễ Giáng Sinh', 'start_date' => '2025-12-24', 'end_date' => '2025-12-25', 'price_increase_percentage' => 20],
            ['name' => 'Lễ Giáng Sinh', 'start_date' => '2026-12-24', 'end_date' => '2026-12-25', 'price_increase_percentage' => 20],

            // ============================================================
            // DỊP NGHỈ LỄ DÀI (Nhà nước quy định bù)
            // ============================================================
            // Nghỉ lễ Quốc khánh 2026 (2/9 là thứ 4, bù thứ 7 5/9)
            ['name' => 'Nghỉ bù Quốc khánh', 'start_date' => '2026-09-03', 'end_date' => '2026-09-04', 'price_increase_percentage' => 20],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                [
                    'name' => $holiday['name'],
                    'start_date' => $holiday['start_date'],
                ],
                [
                    'end_date' => $holiday['end_date'],
                    'price_increase_percentage' => $holiday['price_increase_percentage'],
                ]
            );
        }

        $this->command->info('✅ Đã seed '.count($holidays).' ngày lễ Việt Nam thành công!');
    }
}
