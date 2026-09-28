<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Addon;
use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

// 1. Delete all current 'extra' addons to clean up the mess
DB::table('tour_addons')->whereIn('addon_id', Addon::where('type', 'extra')->pluck('id'))->delete();
Addon::where('type', 'extra')->delete();

// 2. Create the 3 exactly global extra addons
$addon1 = Addon::create([
    'name' => 'Thuê xe lăn',
    'type' => 'extra',
    'price' => 150000,
    'description' => 'Xe lăn tay tiêu chuẩn, hỗ trợ di chuyển cho người cao tuổi/khuyết tật.',
    'is_active' => true,
]);

$addon2 = Addon::create([
    'name' => 'Đồ ăn chay (suất)',
    'type' => 'extra',
    'price' => 100000,
    'description' => 'Thực đơn thuần chay đặc biệt (vui lòng chọn số lượng suất).',
    'is_active' => true,
]);

$addon3 = Addon::create([
    'name' => 'Xe đẩy cho bé',
    'type' => 'extra',
    'price' => 200000,
    'description' => 'Xe đẩy gấp gọn tiện lợi cho trẻ dưới 3 tuổi.',
    'is_active' => true,
]);

$extraIds = [$addon1->id, $addon2->id, $addon3->id];

// 3. Attach them to ALL tours
$tours = Tour::all();
foreach ($tours as $tour) {
    $tour->addons()->syncWithoutDetaching($extraIds);
}

echo 'Successfully created 3 global extra addons and attached them to all '.$tours->count().' tours.';
