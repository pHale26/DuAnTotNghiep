<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => [
                    'vi' => 'Làm thế nào để đặt tour du lịch?',
                    'en' => 'How do I book a tour?',
                    'zh' => '如何预订旅游？',
                ],
                'answer' => [
                    'vi' => 'Để đặt tour, bạn chỉ cần chọn tour yêu thích, chọn ngày khởi hành, điền thông tin và tiến hành thanh toán. Chúng tôi hỗ trợ nhiều phương thức thanh toán như thẻ tín dụng, chuyển khoản ngân hàng và VNPay.',
                    'en' => 'To book a tour, simply select your favorite tour, choose the departure date, fill in your information and proceed to payment. We support multiple payment methods including credit cards, bank transfers and VNPay.',
                    'zh' => '要预订旅游，只需选择您喜欢的旅游，选择出发日期，填写信息并继续付款。我们支持多种付款方式，包括信用卡、银行转账和VNPay。',
                ],
                'category' => [
                    'vi' => 'Đặt tour',
                    'en' => 'Booking',
                    'zh' => '预订',
                ],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => [
                    'vi' => 'Tôi có thể hủy tour đã đặt không?',
                    'en' => 'Can I cancel my booked tour?',
                    'zh' => '我可以取消已预订的旅游吗？',
                ],
                'answer' => [
                    'vi' => 'Có, bạn có thể hủy tour đã đặt. Chính sách hủy phụ thuộc vào thời gian hủy: Hủy trước 7 ngày: hoàn 80%, hủy trước 3 ngày: hoàn 50%, hủy trong vòng 3 ngày: không hoàn tiền.',
                    'en' => 'Yes, you can cancel your booked tour. Cancellation policy depends on timing: Cancel 7+ days before: 80% refund, cancel 3-7 days before: 50% refund, cancel within 3 days: no refund.',
                    'zh' => '是的，您可以取消已预订的旅游。取消政策取决于时间：提前7天以上取消：退款80%，提前3-7天取消：退款50%，3天内取消：不退款。',
                ],
                'category' => [
                    'vi' => 'Đặt tour',
                    'en' => 'Booking',
                    'zh' => '预订',
                ],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => [
                    'vi' => 'Các phương thức thanh toán nào được chấp nhận?',
                    'en' => 'What payment methods are accepted?',
                    'zh' => '接受哪些付款方式？',
                ],
                'answer' => [
                    'vi' => 'Chúng tôi chấp nhận các phương thức thanh toán: Thẻ tín dụng/ghi nợ (Visa, Mastercard), Chuyển khoản ngân hàng, VNPay, Ví điện tử (Momo, ZaloPay). Tất cả giao dịch đều được mã hóa và bảo mật.',
                    'en' => 'We accept: Credit/Debit cards (Visa, Mastercard), Bank transfers, VNPay, E-wallets (Momo, ZaloPay). All transactions are encrypted and secure.',
                    'zh' => '我们接受：信用卡/借记卡（Visa、Mastercard）、银行转账、VNPay、电子钱包（Momo、ZaloPay）。所有交易都经过加密和安全保护。',
                ],
                'category' => [
                    'vi' => 'Thanh toán',
                    'en' => 'Payment',
                    'zh' => '支付',
                ],
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => [
                    'vi' => 'Tour có bao gồm bảo hiểm du lịch không?',
                    'en' => 'Does the tour include travel insurance?',
                    'zh' => '旅游是否包含旅游保险？',
                ],
                'answer' => [
                    'vi' => 'Có, tất cả các tour của chúng tôi đều bao gồm bảo hiểm du lịch cơ bản. Bảo hiểm bao gồm: Tai nạn cá nhân, Chi phí y tế khẩn cấp, Hủy chuyến do lý do bất khả kháng. Bạn có thể mua thêm gói bảo hiểm mở rộng nếu muốn.',
                    'en' => 'Yes, all our tours include basic travel insurance covering: Personal accidents, Emergency medical expenses, Trip cancellation due to force majeure. You can purchase extended coverage if desired.',
                    'zh' => '是的，我们所有的旅游都包括基本旅游保险，涵盖：个人意外、紧急医疗费用、不可抗力导致的行程取消。如果需要，您可以购买扩展保险。',
                ],
                'category' => [
                    'vi' => 'Bảo hiểm',
                    'en' => 'Insurance',
                    'zh' => '保险',
                ],
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => [
                    'vi' => 'Tôi cần chuẩn bị gì cho chuyến đi?',
                    'en' => 'What should I prepare for the trip?',
                    'zh' => '我需要为旅行准备什么？',
                ],
                'answer' => [
                    'vi' => 'Bạn nên chuẩn bị: Giấy tờ tùy thân (CMND/CCCD/Hộ chiếu), Quần áo phù hợp với thời tiết và điểm đến, Thuốc cá nhân nếu có, Tiền mặt và thẻ ngân hàng, Máy ảnh và sạc điện thoại. Chúng tôi sẽ gửi danh sách chi tiết sau khi đặt tour.',
                    'en' => 'You should prepare: Identity documents (ID card/Passport), Appropriate clothing for weather and destination, Personal medications if any, Cash and bank cards, Camera and phone charger. We will send a detailed list after booking.',
                    'zh' => '您应该准备：身份证件（身份证/护照）、适合天气和目的地的服装、个人药物（如有）、现金和银行卡、相机和手机充电器。预订后我们会发送详细清单。',
                ],
                'category' => [
                    'vi' => 'Chuẩn bị',
                    'en' => 'Preparation',
                    'zh' => '准备',
                ],
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => [
                    'vi' => 'Trẻ em có được giảm giá không?',
                    'en' => 'Are there discounts for children?',
                    'zh' => '儿童有折扣吗？',
                ],
                'answer' => [
                    'vi' => 'Có, chúng tôi có giá ưu đãi cho trẻ em: Trẻ em dưới 2 tuổi: Miễn phí (không có ghế riêng), Trẻ em từ 2-11 tuổi: Giảm 30-50% tùy tour, Trẻ em từ 12 tuổi trở lên: Tính như người lớn.',
                    'en' => 'Yes, we offer discounts for children: Under 2 years: Free (no separate seat), 2-11 years: 30-50% discount depending on tour, 12 years and above: Adult price.',
                    'zh' => '是的，我们为儿童提供折扣：2岁以下：免费（无单独座位），2-11岁：根据旅游打30-50%折扣，12岁及以上：成人价格。',
                ],
                'category' => [
                    'vi' => 'Giá cả',
                    'en' => 'Pricing',
                    'zh' => '价格',
                ],
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
