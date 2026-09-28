<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    public function index()
    {
        // Dữ liệu câu hỏi thường gặp - hardcoded
        $faqs = [
            [
                'category' => 'Đặt tour',
                'icon' => 'bi-suitcase-lg',
                'questions' => [
                    [
                        'question' => 'Làm thế nào để đặt tour du lịch?',
                        'answer' => 'Bạn có thể đặt tour dễ dàng bằng cách: 1) Tìm kiếm tour phù hợp trên trang chủ, 2) Xem chi tiết và chọn ngày khởi hành, 3) Điền thông tin hành khách, 4) Thanh toán qua VNPay hoặc chuyển khoản. Hệ thống sẽ gửi email xác nhận ngay sau khi đặt thành công.',
                    ],
                    [
                        'question' => 'Tôi có thể hủy hoặc thay đổi tour đã đặt không?',
                        'answer' => 'Có, bạn có thể hủy tour trong vòng 24 giờ sau khi đặt để được hoàn tiền 100%. Sau thời gian này, phí hủy sẽ được áp dụng theo chính sách của từng tour. Để thay đổi ngày khởi hành, vui lòng liên hệ bộ phận chăm sóc khách hàng ít nhất 7 ngày trước ngày đi.',
                    ],
                    [
                        'question' => 'Có thể đặt tour cho nhóm lớn không?',
                        'answer' => 'Chúng tôi hỗ trợ đặt tour cho nhóm từ 10 người trở lên với nhiều ưu đãi đặc biệt. Vui lòng liên hệ hotline 1900-xxxx hoặc email group@travelwonder.vn để nhận báo giá và tư vấn chi tiết về tour nhóm.',
                    ],
                    [
                        'question' => 'Làm sao để biết tour còn chỗ không?',
                        'answer' => 'Trên trang chi tiết tour, bạn sẽ thấy số chỗ còn trống được cập nhật real-time. Nếu tour gần hết chỗ, hệ thống sẽ hiển thị cảnh báo "Chỉ còn X chỗ trống". Để đảm bảo có chỗ, chúng tôi khuyến khích đặt sớm.',
                    ],
                ],
            ],
            [
                'category' => 'Thanh toán',
                'icon' => 'bi-credit-card',
                'questions' => [
                    [
                        'question' => 'Các hình thức thanh toán nào được chấp nhận?',
                        'answer' => 'Chúng tôi chấp nhận thanh toán qua: VNPay và chuyển khoản ngân hàng. Thông tin tài khoản sẽ được gửi trong email xác nhận đặt tour.',
                    ],
                    [
                        'question' => 'Khi nào tôi cần thanh toán?',
                        'answer' => 'Bạn có thể thanh toán toàn bộ ngay khi đặt hoặc đặt cọc 30% và thanh toán phần còn lại trước 7 ngày khởi hành. Booking chưa thanh toán đủ sẽ tự động hủy sau thời gian quy định.',
                    ],
                    [
                        'question' => 'Tôi có được hoàn tiền nếu hủy tour?',
                        'answer' => 'Chính sách hoàn tiền (áp dụng cho số tiền đã thanh toán trừ đi phí bảo hiểm du lịch): Hủy trước 7 ngày: hoàn 100%, hủy trước 3-7 ngày: hoàn 50%, hủy trong vòng 3 ngày: không hoàn tiền. Mã giảm giá đã sử dụng sẽ không được hoàn lại. Thời gian xử lý hoàn tiền là 7-10 ngày làm việc.',
                    ],
                    [
                        'question' => 'Có áp dụng mã giảm giá không?',
                        'answer' => 'Có, bạn có thể nhập mã giảm giá tại bước thanh toán. Theo dõi fanpage và website để cập nhật các chương trình khuyến mãi mới nhất. Lưu ý mỗi booking chỉ áp dụng được 1 mã giảm giá.',
                    ],
                ],
            ],
            [
                'category' => 'Dịch vụ tour',
                'icon' => 'bi-globe-asia-australia',
                'questions' => [
                    [
                        'question' => 'Tour bao gồm những gì?',
                        'answer' => 'Tour trọn gói thường bao gồm: vé máy bay/xe khách, vé tham quan điểm đến, hướng dẫn viên, bảo hiểm du lịch. Chi tiết cụ thể được nêu rõ trong mục "Bao gồm/Không bao gồm" của mỗi tour.',
                    ],
                    [
                        'question' => 'Tôi cần chuẩn bị gì trước khi đi tour?',
                        'answer' => 'Chuẩn bị CMND/CCCD, thông tin y tế nếu có, thuốc cá nhân, quần áo phù hợp với điểm đến. Hướng dẫn viên sẽ liên hệ trước 2-3 ngày để thông báo giờ khởi hành và điểm tập trung.',
                    ],
                    [
                        'question' => 'Trẻ em đi tour có được giảm giá không?',
                        'answer' => 'Có. Trẻ dưới 2 tuổi: miễn phí (không có ghế riêng), từ 2-5 tuổi: 50% giá người lớn, từ 6-11 tuổi: 75% giá người lớn. Trẻ từ 12 tuổi trở lên tính bằng giá người lớn.',
                    ],
                    [
                        'question' => 'Có hướng dẫn viên đi cùng không?',
                        'answer' => 'Tất cả tour đều có hướng dẫn viên chuyên nghiệp đi cùng. HDV sẽ hỗ trợ suốt hành trình, giải đáp thắc mắc và xử lý các tình huống phát sinh. Thông tin HDV được gửi qua email trước ngày khởi hành.',
                    ],
                ],
            ],
            [
                'category' => 'Tài khoản & Bảo mật',
                'icon' => 'bi-shield-check',
                'questions' => [
                    [
                        'question' => 'Làm sao để tạo tài khoản?',
                        'answer' => 'Nhấn "Đăng ký" ở góc trên cùng, điền email và mật khẩu. Bạn cũng có thể đăng nhập nhanh bằng Google. Tài khoản giúp bạn theo dõi booking, lưu tour yêu thích và nhận ưu đãi độc quyền.',
                    ],
                    [
                        'question' => 'Quên mật khẩu thì làm sao?',
                        'answer' => 'Nhấn "Quên mật khẩu" tại trang đăng nhập, nhập email đã đăng ký. Hệ thống sẽ gửi link đặt lại mật khẩu. Nếu không nhận được email, kiểm tra hộp thư spam hoặc liên hệ support.',
                    ],
                    [
                        'question' => 'Thông tin cá nhân của tôi có được bảo mật không?',
                        'answer' => 'Chúng tôi cam kết bảo mật tuyệt đối thông tin khách hàng theo chuẩn quốc tế. Dữ liệu được mã hóa SSL, không chia sẻ với bên thứ ba khi chưa có sự đồng ý. Đọc thêm tại Chính sách bảo mật.',
                    ],
                ],
            ],

        ];

        return view('frontend.support.index', compact('faqs'));
    }
}
