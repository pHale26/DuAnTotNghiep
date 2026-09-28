<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Addon;
use Illuminate\Contracts\Console\Kernel;

$addons = Addon::where('type', 'extra')->get();
foreach ($addons as $i => $addon) {
    if ($i % 3 == 0) {
        $addon->name = 'Thuê xe lăn';
        $addon->description = 'Xe lăn tay tiêu chuẩn, hỗ trợ di chuyển cho người cao tuổi/khuyết tật.';
        $addon->price = 150000;
    } elseif ($i % 3 == 1) {
        $addon->name = 'Đồ ăn chay (suất)';
        $addon->description = 'Thực đơn thuần chay đặc biệt (vui lòng chọn số lượng suất).';
        $addon->price = 100000;
    } else {
        $addon->name = 'Xe đẩy cho bé';
        $addon->description = 'Xe đẩy gấp gọn tiện lợi cho trẻ dưới 3 tuổi.';
        $addon->price = 200000;
    }
    $addon->save();
}
echo 'Done';
