<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourImage;
use App\Models\TourItinerary;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterTourSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Bắt đầu seed MasterTourSeeder (dữ liệu chi tiết cho HDV checkin)...');

        $destinationsData = [
            'Đà Nẵng' => [
                'desc' => 'Thành phố đáng sống với biển Mỹ Khê và Cầu Vàng nổi tiếng.',
                'image' => 'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                'addons' => [
                    ['name' => 'Xe đưa đón sân bay Đà Nẵng', 'price' => 200000],
                    ['name' => 'Thuê xe máy 1 ngày', 'price' => 150000],
                ],
                'ticket' => [
                    'title' => 'Vé Sun World Ba Na Hills',
                    'desc' => 'Khám phá Cầu Vàng và Làng Pháp trên đỉnh Bà Nà.',
                    'provider' => 'Sun World',
                    'options' => [
                        ['name' => 'Vé người lớn', 'price' => 900000],
                        ['name' => 'Vé trẻ em', 'price' => 750000],
                    ],
                ],
                'tours' => [
                    [
                        'title' => 'Khám phá Đà Nẵng - Hội An - Bà Nà Hills',
                        'days' => 3, 'nights' => 2, 'price' => 3500000,
                        'images' => [
                            'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                            'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Đón khách - Sơn Trà - Ngũ Hành Sơn - Hội An',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón đoàn tại Sân bay Quốc tế Đà Nẵng', 'start' => '08:00:00', 'end' => '08:30:00', 'desc' => 'HDV đón khách tại ga quốc nội, kiểm tra số lượng khách.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Bán đảo Sơn Trà', 'start' => '09:00:00', 'end' => '11:00:00', 'desc' => 'Viếng Chùa Linh Ứng, chiêm bái tượng Phật Bà Quan Âm cao 67m.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa: Bánh tráng cuốn thịt heo', 'start' => '11:30:00', 'end' => '13:00:00', 'desc' => 'Dùng bữa tại nhà hàng Trần (Lê Duẩn).'],
                                    ['type' => 'Attractions', 'title' => 'Khám phá Ngũ Hành Sơn', 'start' => '14:30:00', 'end' => '16:30:00', 'desc' => 'Khám phá động Huyền Không, chùa Tam Thai và Làng đá mỹ nghệ Non Nước.'],
                                    ['type' => 'Entertainment', 'title' => 'Dạo phố cổ Hội An về đêm', 'start' => '17:30:00', 'end' => '20:30:00', 'desc' => 'Tham quan Chùa Cầu, thả đèn hoa đăng trên sông Hoài. Dùng bữa tối đặc sản Hội An (Cao Lầu).'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Trải nghiệm Sun World Ba Na Hills',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Khởi hành đi Bà Nà Hills', 'start' => '08:00:00', 'end' => '09:00:00', 'desc' => 'Xe đón đoàn từ khách sạn di chuyển lên trạm cáp treo Suối Mơ.'],
                                    ['type' => 'Attractions', 'title' => 'Check-in Cầu Vàng', 'start' => '09:30:00', 'end' => '11:30:00', 'desc' => 'HDV đưa khách qua Cầu Vàng, Vườn hoa Le Jardin D\'Amour, Hầm rượu Debay.'],
                                    ['type' => 'Dining', 'title' => 'Ăn buffet trưa tại Làng Pháp', 'start' => '12:00:00', 'end' => '13:30:00', 'desc' => 'Buffet 100 món tại nhà hàng Arapang.'],
                                    ['type' => 'Entertainment', 'title' => 'Vui chơi tại Fantasy Park', 'start' => '14:00:00', 'end' => '16:00:00', 'desc' => 'Tự do chơi các trò chơi mạo hiểm, tháp rơi tự do, xe trượt ống.'],
                                    ['type' => 'Dining', 'title' => 'Ăn tối hải sản bên bờ biển Mỹ Khê', 'start' => '18:30:00', 'end' => '20:00:00', 'desc' => 'Ăn tối tại nhà hàng hải sản Bé Mặn.'],
                                ],
                            ],
                            3 => [
                                'title' => 'Ngày 3: Mua sắm đặc sản - Tiễn khách',
                                'activities' => [
                                    ['type' => 'Dining', 'title' => 'Ăn sáng buffet tại khách sạn', 'start' => '07:00:00', 'end' => '08:30:00', 'desc' => 'Làm thủ tục trả phòng.'],
                                    ['type' => 'Shopping', 'title' => 'Mua sắm tại Chợ Hàn', 'start' => '09:00:00', 'end' => '11:00:00', 'desc' => 'Tự do mua sắm đặc sản làm quà (chả bò, cá rim, mực rim).'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa: Mì Quảng', 'start' => '11:30:00', 'end' => '13:00:00', 'desc' => 'Ăn trưa trước khi ra sân bay.'],
                                    ['type' => 'Transportation', 'title' => 'Tiễn khách ra sân bay', 'start' => '13:30:00', 'end' => '14:30:00', 'desc' => 'HDV làm thủ tục check-in sân bay cho đoàn. Kết thúc lịch trình.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Tour Đà Nẵng - Cù Lao Chàm lặn ngắm san hô',
                        'days' => 2, 'nights' => 1, 'price' => 1800000,
                        'images' => [
                            'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Cảng Cửa Đại - Cù Lao Chàm',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón khách ra Cảng Cửa Đại', 'start' => '07:30:00', 'end' => '08:30:00', 'desc' => 'Xe đón tại khách sạn di chuyển ra cảng Cửa Đại (Hội An).'],
                                    ['type' => 'Transportation', 'title' => 'Lên cano cao tốc', 'start' => '08:45:00', 'end' => '09:15:00', 'desc' => 'Trải nghiệm cảm giác mạnh trên cano cao tốc đến đảo Cù Lao Chàm.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Khu bảo tồn biển', 'start' => '09:30:00', 'end' => '10:30:00', 'desc' => 'Tham quan khu trưng bày sinh vật biển, Chùa Hải Tạng.'],
                                    ['type' => 'Entertainment', 'title' => 'Lặn ngắm san hô (Snorkeling)', 'start' => '10:45:00', 'end' => '12:00:00', 'desc' => 'Thay đồ bơi, trải nghiệm lặn ngắm san hô tại Hòn Dài.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa hải sản trên đảo', 'start' => '12:30:00', 'end' => '14:00:00', 'desc' => 'Dùng bữa tại nhà hàng Bãi Ông với các món hải sản đánh bắt trong ngày.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Tự do tắm biển - Về đất liền',
                                'activities' => [
                                    ['type' => 'Entertainment', 'title' => 'Tự do tắm biển, đi dạo Bãi Hương', 'start' => '08:00:00', 'end' => '10:30:00', 'desc' => 'Tắm biển buổi sáng, tận hưởng không khí trong lành.'],
                                    ['type' => 'Transportation', 'title' => 'Cano đưa đoàn về đất liền', 'start' => '11:00:00', 'end' => '11:30:00', 'desc' => 'Lên cano cao tốc trở về cảng Cửa Đại.'],
                                    ['type' => 'Transportation', 'title' => 'Trả khách tại khách sạn/sân bay', 'start' => '12:00:00', 'end' => '13:00:00', 'desc' => 'Xe đưa quý khách về điểm đón ban đầu.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Đà Nẵng - Huế - Dấu ấn cố đô',
                        'days' => 1, 'nights' => 0, 'price' => 950000,
                        'images' => [
                            'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Xuyên đèo Hải Vân - Đại Nội Huế',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón khách đi Huế', 'start' => '07:30:00', 'end' => '09:30:00', 'desc' => 'Vượt đèo Hải Vân, dừng chân tại Hải Vân Quan ngắm cảnh vịnh Lăng Cô.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Lăng Khải Định', 'start' => '10:00:00', 'end' => '11:30:00', 'desc' => 'Chiêm ngưỡng kiến trúc pha trộn Á - Âu độc đáo của lăng tẩm.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa đặc sản Huế', 'start' => '12:00:00', 'end' => '13:30:00', 'desc' => 'Thưởng thức ẩm thực cung đình hoặc bún bò Huế truyền thống.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Đại Nội Huế', 'start' => '14:00:00', 'end' => '16:00:00', 'desc' => 'HDV đưa đoàn tham quan Ngọ Môn, Điện Thái Hòa, Tử Cấm Thành.'],
                                    ['type' => 'Attractions', 'title' => 'Viếng Chùa Thiên Mụ', 'start' => '16:15:00', 'end' => '17:00:00', 'desc' => 'Ngắm cảnh chiều tà bên dòng sông Hương.'],
                                    ['type' => 'Transportation', 'title' => 'Khởi hành về Đà Nẵng', 'start' => '17:15:00', 'end' => '19:30:00', 'desc' => 'Xe đưa đoàn về lại khách sạn Đà Nẵng.'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Phú Quốc' => [
                'desc' => 'Đảo ngọc thiên đường với những bãi cát trắng và làn nước xanh ngọc.',
                'image' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                'addons' => [
                    ['name' => 'Đưa đón sân bay Phú Quốc', 'price' => 250000],
                    ['name' => 'Set BBQ hải sản bãi biển', 'price' => 800000],
                ],
                'ticket' => [
                    'title' => 'Vé VinWonders & Safari Phú Quốc',
                    'desc' => 'Vui chơi bất tận tại công viên chủ đề lớn nhất Việt Nam.',
                    'provider' => 'Vinpearl',
                    'options' => [
                        ['name' => 'Combo VinWonders + Safari (Người lớn)', 'price' => 1350000],
                        ['name' => 'Combo VinWonders + Safari (Trẻ em)', 'price' => 1000000],
                    ],
                ],
                'tours' => [
                    [
                        'title' => 'Nghỉ dưỡng Vinpearl Phú Quốc trọn gói',
                        'days' => 3, 'nights' => 2, 'price' => 5900000,
                        'images' => [
                            'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Check-in Resort - Vui chơi Grand World',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón sân bay về Vinpearl', 'start' => '10:00:00', 'end' => '11:00:00', 'desc' => 'Xe điện Vinpearl đón khách tại sân bay, đưa về sảnh resort.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa buffet', 'start' => '12:00:00', 'end' => '13:30:00', 'desc' => 'Buffet ẩm thực Á-Âu tại nhà hàng Seashell.'],
                                    ['type' => 'Accommodation', 'title' => 'Nhận phòng nghỉ ngơi', 'start' => '14:00:00', 'end' => '15:30:00', 'desc' => 'Làm thủ tục nhận phòng Ocean View.'],
                                    ['type' => 'Attractions', 'title' => 'Khám phá Grand World', 'start' => '16:00:00', 'end' => '21:00:00', 'desc' => 'Chụp ảnh tại dòng sông Venice, ăn tối tự túc và xem show Tinh Hoa Việt Nam.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Quậy tung VinWonders & Safari',
                                'activities' => [
                                    ['type' => 'Attractions', 'title' => 'Khám phá Vinpearl Safari', 'start' => '09:00:00', 'end' => '12:00:00', 'desc' => 'Lên xe bus xem thú thả tự nhiên, tương tác với các loài động vật hoang dã.'],
                                    ['type' => 'Entertainment', 'title' => 'Vui chơi VinWonders', 'start' => '14:00:00', 'end' => '18:00:00', 'desc' => 'Trải nghiệm công viên nước, các trò chơi cảm giác mạnh, Thủy cung hình rùa khổng lồ.'],
                                    ['type' => 'Entertainment', 'title' => 'Xem show diễn Once', 'start' => '19:00:00', 'end' => '19:30:00', 'desc' => 'Show diễn thực cảnh hoành tráng kết hợp nước, lửa và 3D mapping.'],
                                ],
                            ],
                            3 => [
                                'title' => 'Ngày 3: Mua sắm đặc sản - Tạm biệt Phú Quốc',
                                'activities' => [
                                    ['type' => 'Shopping', 'title' => 'Tham quan Chợ Đêm, mua đặc sản', 'start' => '09:00:00', 'end' => '11:00:00', 'desc' => 'Mua nước mắm, tiêu sọ, ngọc trai về làm quà.'],
                                    ['type' => 'Transportation', 'title' => 'Tiễn khách ra sân bay', 'start' => '12:00:00', 'end' => '13:00:00', 'desc' => 'Xe đưa đoàn ra sân bay Phú Quốc, chào tạm biệt.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Tour cano 4 đảo Phú Quốc & Cáp treo Hòn Thơm',
                        'days' => 1, 'nights' => 0, 'price' => 1200000,
                        'images' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Lặn san hô 4 đảo và đi Cáp treo vượt biển',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón khách tại trung tâm Dương Đông', 'start' => '08:00:00', 'end' => '09:00:00', 'desc' => 'Xe và HDV đón đoàn xuống cảng An Thới.'],
                                    ['type' => 'Entertainment', 'title' => 'Cano tham quan Hòn Móng Tay & Hòn Gầm Ghì', 'start' => '09:30:00', 'end' => '11:30:00', 'desc' => 'Lặn ngắm san hô tại Vương quốc san hô Gầm Ghì.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa tại Hòn Mây Rút', 'start' => '12:00:00', 'end' => '13:30:00', 'desc' => 'Thưởng thức 8 món hải sản biển tươi ngon.'],
                                    ['type' => 'Attractions', 'title' => 'Check-in Cáp treo Hòn Thơm', 'start' => '14:30:00', 'end' => '16:30:00', 'desc' => 'Cano đưa về Hòn Thơm, trải nghiệm cáp treo vượt biển dài nhất thế giới về lại ga An Thới.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Khám phá Nam Đảo - Ngắm hoàng hôn Sunset Sanato',
                        'days' => 1, 'nights' => 0, 'price' => 850000,
                        'images' => [
                            'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Di tích lịch sử và Hoàng hôn trên biển',
                                'activities' => [
                                    ['type' => 'Attractions', 'title' => 'Tham quan Nhà tù Phú Quốc', 'start' => '13:30:00', 'end' => '14:30:00', 'desc' => 'Tìm hiểu di tích lịch sử nhà tù thực dân.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Thiền viện Trúc Lâm Hộ Quốc', 'start' => '15:00:00', 'end' => '16:00:00', 'desc' => 'Ngôi chùa có vị thế phong thủy tuyệt đẹp lưng tựa núi, mặt hướng biển.'],
                                    ['type' => 'Entertainment', 'title' => 'Ngắm hoàng hôn tại Sunset Sanato', 'start' => '16:30:00', 'end' => '18:00:00', 'desc' => 'Chụp ảnh cùng các mô hình nghệ thuật trên bãi biển lúc hoàng hôn.'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Sapa' => [
                'desc' => 'Thị trấn trong sương với những thửa ruộng bậc thang tuyệt đẹp.',
                'image' => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                'addons' => [
                    ['name' => 'Thuê thợ chụp ảnh Sapa (2 giờ)', 'price' => 1000000],
                    ['name' => 'Tắm lá thuốc người Dao đỏ', 'price' => 120000],
                ],
                'ticket' => [
                    'title' => 'Vé cáp treo Fansipan Legend',
                    'desc' => 'Chinh phục nóc nhà Đông Dương.',
                    'provider' => 'Sun World',
                    'options' => [
                        ['name' => 'Vé cáp treo khứ hồi', 'price' => 800000],
                        ['name' => 'Vé tàu hỏa leo núi Mường Hoa', 'price' => 150000],
                    ],
                ],
                'tours' => [
                    [
                        'title' => 'Sapa - Bản Cát Cát - Chinh phục Fansipan',
                        'days' => 3, 'nights' => 2, 'price' => 2800000,
                        'images' => [
                            'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Hà Nội - Sapa - Bản Cát Cát',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Khởi hành bằng xe giường nằm đi Sapa', 'start' => '06:30:00', 'end' => '12:30:00', 'desc' => 'Đón khách tại phố cổ Hà Nội, di chuyển theo cao tốc Nội Bài - Lào Cai.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa tại Sapa', 'start' => '13:00:00', 'end' => '14:00:00', 'desc' => 'Ăn trưa, nhận phòng khách sạn.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Bản Cát Cát', 'start' => '14:30:00', 'end' => '17:30:00', 'desc' => 'Đi bộ tham quan bản của người H\'Mông, ngắm thác thủy điện, thuê trang phục dân tộc check-in.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Chinh phục đỉnh Fansipan - Nóc nhà Đông Dương',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đi tàu hỏa Mường Hoa', 'start' => '08:30:00', 'end' => '09:00:00', 'desc' => 'Từ nhà ga Sun Plaza đi tàu hỏa băng qua thung lũng Mường Hoa.'],
                                    ['type' => 'Attractions', 'title' => 'Trải nghiệm cáp treo lên đỉnh Fansipan', 'start' => '09:30:00', 'end' => '12:00:00', 'desc' => 'Chinh phục độ cao 3.143m, ngắm biển mây và chiêm bái quần thể tâm linh.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa buffet tại nhà hàng Vân Sam', 'start' => '12:30:00', 'end' => '14:00:00', 'desc' => 'Thưởng thức ẩm thực Tây Bắc.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Đèo Ô Quy Hồ', 'start' => '15:30:00', 'end' => '17:30:00', 'desc' => 'Chụp ảnh tại đèo Ô Quy Hồ - một trong tứ đại đỉnh đèo.'],
                                ],
                            ],
                            3 => [
                                'title' => 'Ngày 3: Chợ Sapa - Về lại Hà Nội',
                                'activities' => [
                                    ['type' => 'Shopping', 'title' => 'Tự do dạo chợ Sapa mua sắm', 'start' => '08:30:00', 'end' => '11:00:00', 'desc' => 'Mùa quà lưu niệm, măng khô, nấm hương rừng.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa lẩu cá tầm', 'start' => '11:30:00', 'end' => '13:00:00', 'desc' => 'Trả phòng và dùng bữa trưa lẩu cá tầm đặc sản.'],
                                    ['type' => 'Transportation', 'title' => 'Lên xe giường nằm về Hà Nội', 'start' => '14:00:00', 'end' => '20:00:00', 'desc' => 'Đoàn về tới Hà Nội. Kết thúc hành trình.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Trekking bản Lao Chải - Tả Van',
                        'days' => 2, 'nights' => 1, 'price' => 1500000,
                        'images' => [
                            'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Trekking Lao Chải - Tả Van',
                                'activities' => [
                                    ['type' => 'Entertainment', 'title' => 'Đi bộ dọc thung lũng Mường Hoa', 'start' => '09:00:00', 'end' => '12:00:00', 'desc' => 'HDV địa phương dẫn đi bộ qua các ruộng bậc thang tuyệt đẹp.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa tại bản', 'start' => '12:30:00', 'end' => '13:30:00', 'desc' => 'Dùng bữa trưa do người dân tộc chuẩn bị.'],
                                    ['type' => 'Accommodation', 'title' => 'Nhận Homestay tại Tả Van', 'start' => '15:00:00', 'end' => '16:00:00', 'desc' => 'Nhận chỗ nghỉ, tự do nghỉ ngơi hoặc dạo quanh bản.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Bản Giang Tà Chải - Sapa',
                                'activities' => [
                                    ['type' => 'Entertainment', 'title' => 'Trekking xuyên rừng tre', 'start' => '08:30:00', 'end' => '11:30:00', 'desc' => 'Trekking đến thác nước Giang Tà Chải.'],
                                    ['type' => 'Transportation', 'title' => 'Xe đón về thị trấn', 'start' => '12:00:00', 'end' => '13:00:00', 'desc' => 'Lên xe jeep trở về thị trấn Sapa ăn trưa.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Sapa mùa lúa chín - Check-in Moana',
                        'days' => 2, 'nights' => 1, 'price' => 1800000,
                        'images' => [
                            'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Tham quan Moana Sapa',
                                'activities' => [
                                    ['type' => 'Attractions', 'title' => 'Check-in phim trường Moana', 'start' => '14:30:00', 'end' => '16:30:00', 'desc' => 'Chụp ảnh với tượng cô gái Moana, Cổng trời Bali, Hồ vô cực.'],
                                    ['type' => 'Entertainment', 'title' => 'Dạo phố nướng Sapa', 'start' => '19:00:00', 'end' => '21:00:00', 'desc' => 'Thưởng thức các xiên nướng đặc sản Sapa trong tiết trời se lạnh.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Chinh phục Hàm Rồng',
                                'activities' => [
                                    ['type' => 'Attractions', 'title' => 'Núi Hàm Rồng - Sân Mây', 'start' => '08:30:00', 'end' => '11:30:00', 'desc' => 'Ngắm toàn cảnh thị trấn Sapa từ trên Sân Mây, tham quan vườn lan.'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            // Hà Nội, Đà Lạt, Hạ Long also with detailed itineraries...
            'Hà Nội' => [
                'desc' => 'Thủ đô ngàn năm văn hiến, cổ kính và yên bình.',
                'image' => 'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                'addons' => [
                    ['name' => 'Đưa đón sân bay Nội Bài', 'price' => 300000],
                    ['name' => 'Xích lô phố cổ (1 giờ)', 'price' => 150000],
                ],
                'ticket' => [
                    'title' => 'Vé rối nước Thăng Long',
                    'desc' => 'Thưởng thức nghệ thuật múa rối nước truyền thống.',
                    'provider' => 'Nhà hát múa rối Thăng Long',
                    'options' => [
                        ['name' => 'Vé VIP', 'price' => 200000],
                        ['name' => 'Vé Thường', 'price' => 150000],
                    ],
                ],
                'tours' => [
                    [
                        'title' => 'Hà Nội City Tour Tuyến Cổ Điển',
                        'days' => 1, 'nights' => 0, 'price' => 800000,
                        'images' => [
                            'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Lăng Bác - Văn Miếu - Phố Cổ',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón khách tại khách sạn phố cổ', 'start' => '08:00:00', 'end' => '08:30:00', 'desc' => 'HDV và xe đón quý khách đi tham quan.'],
                                    ['type' => 'Attractions', 'title' => 'Viếng Lăng Bác & Chùa Một Cột', 'start' => '08:45:00', 'end' => '10:30:00', 'desc' => 'Vào lăng viếng Bác, tham quan Phủ Chủ tịch, ao cá, Chùa Một Cột.'],
                                    ['type' => 'Attractions', 'title' => 'Văn Miếu - Quốc Tử Giám', 'start' => '10:45:00', 'end' => '12:00:00', 'desc' => 'Thăm trường đại học đầu tiên của Việt Nam.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa Bún Chả Hà Nội', 'start' => '12:15:00', 'end' => '13:30:00', 'desc' => 'Thưởng thức đặc sản bún chả nướng than hoa.'],
                                    ['type' => 'Attractions', 'title' => 'Bảo tàng Dân tộc học', 'start' => '14:00:00', 'end' => '15:30:00', 'desc' => 'Tìm hiểu văn hóa 54 dân tộc anh em.'],
                                    ['type' => 'Entertainment', 'title' => 'Ngắm Hồ Gươm - Đền Ngọc Sơn', 'start' => '16:00:00', 'end' => '17:00:00', 'desc' => 'Chụp ảnh tại Cầu Thê Húc, Tháp Rùa.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Hà Nội - Ninh Bình (Hoa Lư - Tam Cốc)',
                        'days' => 1, 'nights' => 0, 'price' => 950000,
                        'images' => [
                            'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Cố đô Hoa Lư - Tam Cốc',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Khởi hành đi Ninh Bình', 'start' => '07:30:00', 'end' => '10:00:00', 'desc' => 'Đi cao tốc Pháp Vân - Cầu Giẽ.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan cố đô Hoa Lư', 'start' => '10:15:00', 'end' => '11:30:00', 'desc' => 'Viếng đền vua Đinh, vua Lê.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa Buffet thịt dê', 'start' => '12:00:00', 'end' => '13:00:00', 'desc' => 'Thưởng thức đặc sản dê núi cơm cháy.'],
                                    ['type' => 'Entertainment', 'title' => 'Ngồi đò thăm Tam Cốc', 'start' => '13:30:00', 'end' => '15:30:00', 'desc' => 'Đi đò chèo tay qua 3 hang động tuyệt đẹp (Hang Cả, Hang Hai, Hang Ba).'],
                                    ['type' => 'Transportation', 'title' => 'Lên xe về Hà Nội', 'start' => '16:00:00', 'end' => '18:30:00', 'desc' => 'Trả khách tại điểm đón ban đầu.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Food Tour phố cổ Hà Nội về đêm',
                        'days' => 1, 'nights' => 0, 'price' => 600000,
                        'images' => [
                            'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Khám phá ẩm thực Phố Cổ',
                                'activities' => [
                                    ['type' => 'Entertainment', 'title' => 'Đi bộ len lỏi ngõ ngách Phố Cổ', 'start' => '18:00:00', 'end' => '18:30:00', 'desc' => 'HDV dẫn đường qua những con phố chật hẹp mang đậm dấu ấn thời gian.'],
                                    ['type' => 'Dining', 'title' => 'Thưởng thức Phở và Nem rán', 'start' => '18:30:00', 'end' => '19:30:00', 'desc' => 'Ăn phở Bát Đàn, nem cua bể.'],
                                    ['type' => 'Dining', 'title' => 'Uống Cafe Trứng', 'start' => '20:00:00', 'end' => '21:00:00', 'desc' => 'Trải nghiệm cafe trứng Giảng nóng hổi thơm ngon.'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Đà Lạt' => [
                'desc' => 'Thành phố ngàn hoa với không khí se lạnh quanh năm.',
                'image' => 'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                'addons' => [
                    ['name' => 'Thuê xe máy tay ga', 'price' => 200000],
                    ['name' => 'Chụp ảnh chuyên nghiệp (1 buổi)', 'price' => 800000],
                ],
                'ticket' => [
                    'title' => 'Vé Cáp treo Robin - Thiền Viện Trúc Lâm',
                    'desc' => 'Ngắm nhìn rừng thông từ trên cao.',
                    'provider' => 'KDL Cáp treo Đà Lạt',
                    'options' => [
                        ['name' => 'Khứ hồi', 'price' => 100000],
                        ['name' => 'Một chiều', 'price' => 80000],
                    ],
                ],
                'tours' => [
                    [
                        'title' => 'Đà Lạt 3N2Đ - Chinh phục Langbiang',
                        'days' => 3, 'nights' => 2, 'price' => 2500000,
                        'images' => [
                            'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Check-in Quảng Trường - Hồ Xuân Hương',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón sân bay Liên Khương', 'start' => '10:00:00', 'end' => '11:00:00', 'desc' => 'Về khách sạn cất hành lý.'],
                                    ['type' => 'Attractions', 'title' => 'Quảng trường Lâm Viên', 'start' => '14:00:00', 'end' => '16:00:00', 'desc' => 'Check-in nụ hoa Atiso và hoa Dã quỳ bằng kính khổng lồ.'],
                                    ['type' => 'Dining', 'title' => 'Ăn tối Lẩu gà lá é', 'start' => '18:30:00', 'end' => '19:30:00', 'desc' => 'Thưởng thức lẩu gà lá é Tao Ngộ đặc sản.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: KDL Langbiang - Giao lưu văn hóa Cồng Chiêng',
                                'activities' => [
                                    ['type' => 'Attractions', 'title' => 'Tham quan Thung lũng Tình Yêu', 'start' => '08:30:00', 'end' => '11:00:00', 'desc' => 'Dạo chơi trong thung lũng lãng mạn ngập tràn sắc hoa.'],
                                    ['type' => 'Entertainment', 'title' => 'Chinh phục đỉnh Langbiang bằng xe Jeep', 'start' => '14:00:00', 'end' => '16:30:00', 'desc' => 'Ngắm toàn cảnh suối Vàng suối Bạc từ radar.'],
                                    ['type' => 'Entertainment', 'title' => 'Giao lưu văn hóa Cồng Chiêng Tây Nguyên', 'start' => '18:30:00', 'end' => '21:00:00', 'desc' => 'Ăn thịt nướng, uống rượu cần, nhảy múa cùng đồng bào K\'Ho.'],
                                ],
                            ],
                            3 => [
                                'title' => 'Ngày 3: Thiền Viện Trúc Lâm - Mua sắm',
                                'activities' => [
                                    ['type' => 'Attractions', 'title' => 'Trải nghiệm cáp treo Đồi Robin', 'start' => '08:30:00', 'end' => '10:00:00', 'desc' => 'Đi cáp treo băng qua rừng thông xuống Thiền Viện Trúc Lâm.'],
                                    ['type' => 'Shopping', 'title' => 'Ghé Chợ Đà Lạt', 'start' => '10:30:00', 'end' => '12:00:00', 'desc' => 'Mua dâu tây, hoa quả sấy, mứt.'],
                                    ['type' => 'Transportation', 'title' => 'Ra sân bay', 'start' => '13:00:00', 'end' => '14:00:00', 'desc' => 'Xe đưa đoàn ra sân bay.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Săn Mây Đồi Chè Cầu Đất - Tour Nửa Ngày',
                        'days' => 1, 'nights' => 0, 'price' => 450000,
                        'images' => [
                            'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Sáng: Điểm săn mây - Đồi chè Cầu Đất',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Xe đón tại khách sạn lúc tờ mờ sáng', 'start' => '04:00:00', 'end' => '05:00:00', 'desc' => 'Di chuyển nhanh để kịp đón bình minh.'],
                                    ['type' => 'Attractions', 'title' => 'Đón bình minh trên thảm mây', 'start' => '05:30:00', 'end' => '07:00:00', 'desc' => 'Check-in thảm gỗ săn mây, uống cafe nóng giữa sương sớm.'],
                                    ['type' => 'Attractions', 'title' => 'Tham quan Đồi chè Cầu Đất', 'start' => '07:30:00', 'end' => '09:00:00', 'desc' => 'Chụp ảnh cùng đồi chè xanh ngát và tuabin điện gió khổng lồ.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Cắm trại thung lũng Vàng, tiệc nướng BBQ',
                        'days' => 2, 'nights' => 1, 'price' => 1800000,
                        'images' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Dựng lều - Tiệc BBQ',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Tập trung vào Thung Lũng Vàng', 'start' => '14:00:00', 'end' => '15:00:00', 'desc' => 'HDV hướng dẫn trekking nhẹ vào khu cắm trại.'],
                                    ['type' => 'Entertainment', 'title' => 'Dựng lều, chuẩn bị lửa trại', 'start' => '15:30:00', 'end' => '17:30:00', 'desc' => 'Tham gia dựng lều Mông Cổ cao cấp.'],
                                    ['type' => 'Dining', 'title' => 'Tiệc nướng BBQ ngoài trời', 'start' => '18:30:00', 'end' => '21:00:00', 'desc' => 'Thưởng thức bò nướng tảng, sườn cừu, nhâm nhi rượu vang bên bếp lửa.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Bình minh bên hồ - Cafe sáng',
                                'activities' => [
                                    ['type' => 'Dining', 'title' => 'Pha cafe, ăn sáng nhẹ', 'start' => '06:30:00', 'end' => '08:00:00', 'desc' => 'Uống cafe phin ngắm sương bay trên mặt hồ. Thu dọn lều trại và kết thúc lịch trình.'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Hạ Long' => [
                'desc' => 'Kỳ quan thiên nhiên thế giới với hàng nghìn đảo đá vôi kỳ vĩ.',
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                'addons' => [
                    ['name' => 'Chèo thuyền Kayak (2 người)', 'price' => 200000],
                    ['name' => 'Xe Limousine Hà Nội - Hạ Long', 'price' => 350000],
                ],
                'ticket' => [
                    'title' => 'Vé Sun World Hạ Long Park',
                    'desc' => 'Công viên giải trí ven biển lớn nhất miền Bắc.',
                    'provider' => 'Sun World',
                    'options' => [
                        ['name' => 'Combo Cáp treo Nữ Hoàng + Công Viên Nước', 'price' => 600000],
                        ['name' => 'Vé lẻ Công Viên Rồng (Dragon Park)', 'price' => 300000],
                    ],
                ],
                'tours' => [
                    [
                        'title' => 'Du thuyền 5 sao Vịnh Hạ Long',
                        'days' => 2, 'nights' => 1, 'price' => 3200000,
                        'images' => [
                            'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Check-in Du thuyền - Hang Sửng Sốt',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón khách tại Tuần Châu', 'start' => '11:30:00', 'end' => '12:00:00', 'desc' => 'Làm thủ tục lên du thuyền 5 sao, nhận ly nước welcome.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa buffet trên thuyền', 'start' => '13:00:00', 'end' => '14:30:00', 'desc' => 'Vừa thưởng thức hải sản vừa ngắm hàng ngàn đảo đá.'],
                                    ['type' => 'Attractions', 'title' => 'Khám phá Hang Sửng Sốt', 'start' => '15:00:00', 'end' => '16:30:00', 'desc' => 'Tham quan hang động lớn và đẹp nhất vịnh Hạ Long.'],
                                    ['type' => 'Entertainment', 'title' => 'Tiệc trà chiều Sunset Party', 'start' => '17:30:00', 'end' => '18:30:00', 'desc' => 'Ngắm hoàng hôn trên boong tàu, tham gia lớp học nấu ăn nhỏ.'],
                                    ['type' => 'Entertainment', 'title' => 'Câu mực đêm', 'start' => '20:30:00', 'end' => '22:00:00', 'desc' => 'Trải nghiệm làm ngư dân câu mực đêm từ đuôi thuyền.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Tập Thái Cực Quyền - Đảo Ti Tốp',
                                'activities' => [
                                    ['type' => 'Entertainment', 'title' => 'Tập Tai Chi trên sundeck', 'start' => '06:00:00', 'end' => '06:45:00', 'desc' => 'Đón bình minh và tập Thái Cực Quyền.'],
                                    ['type' => 'Attractions', 'title' => 'Chinh phục đỉnh Ti Tốp', 'start' => '07:30:00', 'end' => '09:00:00', 'desc' => 'Leo 400 bậc đá lên đỉnh đảo ngắm toàn cảnh Vịnh, hoặc tắm biển.'],
                                    ['type' => 'Dining', 'title' => 'Ăn Brunch (Sáng trưa kết hợp)', 'start' => '10:00:00', 'end' => '11:00:00', 'desc' => 'Ăn nhẹ trong lúc tàu quay về bến.'],
                                    ['type' => 'Transportation', 'title' => 'Cập bến, tiễn khách', 'start' => '11:30:00', 'end' => '12:00:00', 'desc' => 'Kết thúc chương trình nghỉ dưỡng trên Vịnh.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Tour Hạ Long - Vui chơi Sun World Park',
                        'days' => 3, 'nights' => 2, 'price' => 2800000,
                        'images' => [
                            'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Vui chơi Sun World Hạ Long',
                                'activities' => [
                                    ['type' => 'Accommodation', 'title' => 'Nhận phòng tại Bãi Cháy', 'start' => '14:00:00', 'end' => '14:30:00', 'desc' => 'Check-in khách sạn, nghỉ ngơi.'],
                                    ['type' => 'Entertainment', 'title' => 'Quậy tung Dragon Park', 'start' => '15:00:00', 'end' => '18:00:00', 'desc' => 'Tham gia tàu lượn siêu tốc Phi Long Thần Tốc lớn nhất châu Á.'],
                                    ['type' => 'Entertainment', 'title' => 'Đi Cáp treo Nữ Hoàng', 'start' => '19:00:00', 'end' => '21:00:00', 'desc' => 'Ngắm Vịnh Hạ Long lung linh về đêm từ Vòng quay mặt trời Sun Wheel.'],
                                ],
                            ],
                            2 => [
                                'title' => 'Ngày 2: Tắm biển Bãi Cháy - Food Tour',
                                'activities' => [
                                    ['type' => 'Entertainment', 'title' => 'Tự do tắm biển nhân tạo', 'start' => '08:00:00', 'end' => '11:00:00', 'desc' => 'Tắm biển Bãi Cháy, nước trong xanh cát trắng mịn.'],
                                    ['type' => 'Dining', 'title' => 'Ăn trưa Chả Mực Giã Tay', 'start' => '12:00:00', 'end' => '13:30:00', 'desc' => 'Thưởng thức xôi chả mực, bánh cuốn chả mực ngon trứ danh.'],
                                ],
                            ],
                            3 => [
                                'title' => 'Ngày 3: Mua sắm Chợ Hải Sản',
                                'activities' => [
                                    ['type' => 'Shopping', 'title' => 'Đi chợ Cái Dăm', 'start' => '08:30:00', 'end' => '10:30:00', 'desc' => 'Mua mực khô, sá sùng, ruốc tôm về làm quà. Kết thúc chuyến đi.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Tham quan Vịnh Hạ Long Tuyến 2 (6 Tiếng)',
                        'days' => 1, 'nights' => 0, 'price' => 1050000,
                        'images' => [
                            'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                        ],
                        'itinerary' => [
                            1 => [
                                'title' => 'Ngày 1: Chèo Kayak Hang Luồn - Titop',
                                'activities' => [
                                    ['type' => 'Transportation', 'title' => 'Đón tàu tại Cảng Quốc tế Hạ Long', 'start' => '12:00:00', 'end' => '12:30:00', 'desc' => 'Lên tàu ghép khởi hành tuyến 2.'],
                                    ['type' => 'Attractions', 'title' => 'Ngắm hòn Chó Đá, Đỉnh Hương, Trống Mái', 'start' => '13:00:00', 'end' => '14:00:00', 'desc' => 'Đi qua các biểu tượng nổi tiếng của Vịnh Hạ Long.'],
                                    ['type' => 'Entertainment', 'title' => 'Chèo Kayak tại Hang Luồn', 'start' => '14:30:00', 'end' => '15:30:00', 'desc' => 'Tự chèo thuyền Kayak hoặc ngồi đò nan xuyên qua vòm đá tự nhiên.'],
                                    ['type' => 'Attractions', 'title' => 'Đảo Ti Tốp', 'start' => '16:00:00', 'end' => '17:00:00', 'desc' => 'Tắm biển trên bãi cát hình vầng trăng khuyết.'],
                                    ['type' => 'Transportation', 'title' => 'Về bến', 'start' => '17:30:00', 'end' => '18:00:00', 'desc' => 'Tàu cập bến kết thúc chương trình.'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $categories = Category::all();
        $fallbackCategory = $categories->first();

        foreach ($destinationsData as $destName => $destInfo) {
            // Create Destination
            $destination = Destination::firstOrCreate(
                ['name' => $destName],
                [
                    'description' => $destInfo['desc'],
                    'image_url' => $destInfo['image'],
                ]
            );

            // Create Addons
            $createdAddons = [];
            foreach ($destInfo['addons'] as $addonData) {
                $createdAddons[] = Addon::firstOrCreate(
                    ['name' => $addonData['name']],
                    [
                        'description' => 'Dịch vụ tiện ích tại '.$destName,
                        'price' => $addonData['price'],
                        'is_active' => true,
                        'type' => 'included', // Set old ones to included
                    ]
                );
            }

            // Create global extra addons requested by user
            $extraAddons = [
                ['name' => 'Thuê xe lăn', 'price' => 150000, 'description' => 'Xe lăn tay tiêu chuẩn, hỗ trợ di chuyển cho người cao tuổi/khuyết tật.'],
                ['name' => 'Đồ ăn chay (suất)', 'price' => 100000, 'description' => 'Thực đơn thuần chay đặc biệt (vui lòng chọn số lượng suất).'],
                ['name' => 'Xe đẩy cho bé', 'price' => 200000, 'description' => 'Xe đẩy gấp gọn tiện lợi cho trẻ dưới 3 tuổi.'],
            ];
            foreach ($extraAddons as $ext) {
                $createdAddons[] = Addon::firstOrCreate(
                    ['name' => $ext['name']],
                    [
                        'description' => $ext['description'],
                        'price' => $ext['price'],
                        'is_active' => true,
                        'type' => 'extra',
                    ]
                );
            }

            // Create Ticket
            $ticket = Ticket::firstOrCreate(
                ['title' => $destInfo['ticket']['title']],
                [
                    'destination_id' => $destination->id,
                    'slug' => Str::slug($destInfo['ticket']['title'].'-'.time()),
                    'description' => $destInfo['ticket']['desc'],
                    'provider_name' => $destInfo['ticket']['provider'],
                    'cancellation_policy' => 'Vé không hỗ trợ hủy hoặc hoàn tiền',
                ]
            );
            foreach ($destInfo['ticket']['options'] as $opt) {
                TicketOption::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'name' => $opt['name'],
                    ],
                    [
                        'description' => 'Vé tiêu chuẩn',
                        'price' => $opt['price'],
                        'original_price' => $opt['price'] + 50000,
                    ]
                );
            }
            // Ticket Image
            TicketImage::firstOrCreate([
                'ticket_id' => $ticket->id,
                'image_url' => $destInfo['image'],
                'is_primary' => true,
            ]);

            // Create Tours
            foreach ($destInfo['tours'] as $tourData) {
                $tour = Tour::firstOrCreate(
                    ['title' => $tourData['title']],
                    [
                        'destination_id' => $destination->id,
                        'slug' => Str::slug($tourData['title'].'-'.time()),
                        'description' => 'Hành trình tuyệt vời khám phá '.$destName.' trọn gói với nhiều trải nghiệm hấp dẫn và dịch vụ cao cấp.',
                        'duration_days' => $tourData['days'],
                        'duration_nights' => $tourData['nights'],
                        'base_price' => $tourData['price'],
                        'child_price' => $tourData['price'] * 0.7,
                        'departure_time' => '08:00:00',
                        'meeting_point' => 'Sân bay / Bến xe trung tâm '.$destName,
                    ]
                );

                // Category Sync
                if ($fallbackCategory) {
                    $tour->categories()->sync([$fallbackCategory->id]);
                }

                // Images
                foreach ($tourData['images'] as $index => $imgUrl) {
                    TourImage::firstOrCreate([
                        'tour_id' => $tour->id,
                        'image_url' => $imgUrl,
                        'is_primary' => ($index === 0),
                    ]);
                }

                // Schedules
                // Tăng lên 8 lịch khởi hành mỗi tour để rải đều từ mùng 1 đến cuối tháng 8
                // Cố định mốc bắt đầu là 01/08/2026
                $baseDate = Carbon::create(2026, 8, 1, 8, 0, 0);
                for ($i = 0; $i < 8; $i++) {
                    // Cứ mỗi 4 ngày sẽ có 1 chuyến đi, cộng thêm ngẫu nhiên 0-2 ngày
                    $depDate = (clone $baseDate)->addDays($i * 4 + rand(0, 2));
                    $retDate = (clone $depDate)->addDays($tourData['days']);

                    // Ngẫu nhiên số chỗ để UI hiển thị trạng thái xanh/vàng/đỏ chân thực hơn
                    $randCapacity = rand(15, 30);
                    $randAvail = rand(0, $randCapacity);
                    $status = ($randAvail === 0) ? 'full' : 'available';

                    TourSchedule::firstOrCreate(
                        [
                            'tour_id' => $tour->id,
                            'departure_date' => $depDate->toDateTimeString(),
                        ],
                        [
                            'return_date' => $retDate->toDateTimeString(),
                            'capacity' => $randCapacity,
                            'available_seats' => $randAvail,
                            'status' => $status,
                        ]
                    );
                }

                // Itineraries & Activities (Sử dụng dữ liệu chi tiết đã khai báo)
                if (isset($tourData['itinerary'])) {
                    foreach ($tourData['itinerary'] as $dayNumber => $dayInfo) {
                        $itinerary = TourItinerary::firstOrCreate([
                            'tour_id' => $tour->id,
                            'day_number' => $dayNumber,
                        ], [
                            'title' => $dayInfo['title'],
                            'description' => 'Chi tiết lịch trình hoạt động trong ngày.',
                        ]);

                        if (isset($dayInfo['activities'])) {
                            foreach ($dayInfo['activities'] as $act) {
                                TourActivity::firstOrCreate([
                                    'tour_itinerary_id' => $itinerary->id,
                                    'title' => $act['title'],
                                    'start_time' => $act['start'],
                                ], [
                                    'activity_type' => $act['type'],
                                    'end_time' => $act['end'],
                                    'description' => $act['desc'],
                                ]);
                            }
                        }
                    }
                }

                // Sync Addons
                $addonIds = array_map(fn ($a) => $a->id, $createdAddons);
                $tour->addons()->sync($addonIds);

                // Sync Tickets
                $tour->tickets()->sync([$ticket->id]);

                // Calculate cost breakdown to match base_price
                $ticketAdultCost = $tour->tickets()->sum('adult_price');
                $ticketChildCost = $tour->tickets()->sum('child_price');
                $targetBase = (float) ($tourData['price'] > 0 ? $tourData['price'] : 1000000);
                if ($ticketAdultCost >= $targetBase) {
                    $targetBase = $ticketAdultCost + 500000;
                }
                $remaining = max(0, $targetBase - $ticketAdultCost);
                $transport = round($remaining * 0.35, -3);
                $meal = round($remaining * 0.35, -3);
                $insurance = round($remaining * 0.05, -3);
                $service = $remaining - ($transport + $meal + $insurance);
                $childRate = config('booking.child_price_rate', 0.7);

                $tour->cost_transport = $transport;
                $tour->cost_meal = $meal;
                $tour->cost_insurance = $insurance;
                $tour->cost_service_fee = $service;
                $tour->base_price = $transport + $meal + $insurance + $service + $ticketAdultCost;
                $tour->child_price = round((($transport + $meal + $insurance + $service) * $childRate) + $ticketChildCost, -3);
                $tour->save();
            }
        }

        $this->command->info('Hoàn thành MasterTourSeeder! Dữ liệu đã cực kỳ chi tiết cho HDV.');
    }
}
