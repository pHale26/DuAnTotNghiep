<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $tours = Tour::with('destination')->get();
        $users = User::all();

        if ($users->isEmpty() || $tours->isEmpty()) {
            $this->command->info('Không có user hoặc tour nào để tạo review.');

            return;
        }

        DB::table('reviews')->truncate();

        $positiveComments = [
            'Chuyến đi %s thật tuyệt vời, gia đình tôi rất thích!',
            'Hướng dẫn viên nhiệt tình, cảnh quan %s đẹp ngoài sức tưởng tượng.',
            'Dịch vụ hoàn hảo, đáng từng đồng tiền bát gạo cho tour %s này.',
            'Tôi chắc chắn sẽ quay lại %s một lần nữa cùng TravelWonder.',
            'Lịch trình %s được sắp xếp hợp lý, ăn uống ngon miệng.',
            'Rất ưng ý với tour %s. Mọi thứ đều xuất sắc.',
        ];

        $neutralComments = [
            'Chuyến đi %s tạm ổn, tuy nhiên thời gian di chuyển hơi nhiều.',
            'Tour %s bình thường, không có gì quá nổi bật nhưng cũng không tệ.',
            'Khách sạn ở %s khá xa trung tâm, bù lại đồ ăn tạm được.',
            'Hướng dẫn viên nhiệt tình nhưng lịch trình %s hơi gấp gáp.',
        ];

        $negativeComments = [
            'Rất thất vọng về dịch vụ của tour %s.',
            'Thời tiết xấu làm hỏng chuyến đi %s, công ty hỗ trợ chưa tốt.',
            'Chất lượng bữa ăn trong tour %s quá tệ, không như mong đợi.',
        ];

        foreach ($tours as $tour) {
            $numReviews = rand(5, 12);
            $randomUsers = $users->random(min($numReviews, $users->count()));

            $tourName = $tour->destination ? $tour->destination->name : $tour->title;
            // Get a short version of the title if it's too long
            if (mb_strlen($tourName) > 30) {
                $tourName = mb_substr($tourName, 0, 30).'...';
            }

            foreach ($randomUsers as $user) {
                $randVal = rand(1, 100);

                if ($randVal <= 70) {
                    $rating = rand(4, 5);
                    $template = $positiveComments[array_rand($positiveComments)];
                } elseif ($randVal <= 90) {
                    $rating = 3;
                    $template = $neutralComments[array_rand($neutralComments)];
                } else {
                    $rating = rand(1, 2);
                    $template = $negativeComments[array_rand($negativeComments)];
                }

                $comment = sprintf($template, $tourName);

                Review::create([
                    'user_id' => $user->id,
                    'tour_id' => $tour->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_hidden' => false,
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }

        $this->command->info('Đã tạo review thành công!');
    }
}
