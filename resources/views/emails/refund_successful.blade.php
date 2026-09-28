<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo hoàn tiền thành công</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; color: #333; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #28a745; padding: 20px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; line-height: 1.6; }
        .info-box { background: #f8f9fa; border: 1px solid #e9ecef; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 8px 0; vertical-align: top; }
        .info-box td:first-child { font-weight: bold; width: 45%; color: #555; }
        .footer { text-align: center; padding: 20px; font-size: 14px; color: #777; background: #f4f4f4; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hoàn tiền thành công</h1>
        </div>
        <div class="content">
            <p>Chào <strong>{{ $refund->booking->customer_name }}</strong>,</p>
            <p>Chúng tôi xin thông báo yêu cầu hoàn tiền cho đơn đặt chỗ <strong>{{ $refund->booking->code }}</strong> của bạn đã được xử lý thành công. Dưới đây là chi tiết giao dịch hoàn tiền:</p>
            
            <div class="info-box">
                <table>
                    <tr>
                        <td>Mã đơn đặt chỗ:</td>
                        <td>{{ $refund->booking->code }}</td>
                    </tr>
                    <tr>
                        <td>Số tiền hoàn lại:</td>
                        <td style="color: #28a745; font-weight: bold; font-size: 16px;">{{ number_format($refund->amount, 0, ',', '.') }} VNĐ</td>
                    </tr>
                    <tr>
                        <td>Phương thức hoàn tiền:</td>
                        <td>
                            @if($refund->refund_method == 'bank_transfer')
                                Chuyển khoản ngân hàng
                            @elseif($refund->refund_method == 'cash')
                                Tiền mặt
                            @elseif($refund->refund_method == 'credit_card')
                                Thẻ tín dụng
                            @else
                                {{ ucfirst($refund->refund_method) }}
                            @endif
                        </td>
                    </tr>
                    
                    @if($refund->refund_method == 'bank_transfer' && $refund->bank_name)
                    <tr>
                        <td>Ngân hàng thụ hưởng:</td>
                        <td>{{ $refund->bank_name }}</td>
                    </tr>
                    <tr>
                        <td>Tên tài khoản:</td>
                        <td>{{ $refund->bank_account_name }}</td>
                    </tr>
                    <tr>
                        <td>Số tài khoản:</td>
                        <td>
                            @if(strlen($refund->bank_account_number) >= 6)
                                *******{{ substr($refund->bank_account_number, -4) }}
                            @else
                                {{ $refund->bank_account_number }}
                            @endif
                        </td>
                    </tr>
                    @endif

                    @if($refund->transaction_reference)
                    <tr>
                        <td>Mã giao dịch:</td>
                        <td>{{ $refund->transaction_reference }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td>Thời gian xử lý:</td>
                        <td>{{ $refund->processed_at ? $refund->processed_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
                    </tr>

                    @if($refund->notes)
                    <tr>
                        <td>Ghi chú thêm:</td>
                        <td>{{ $refund->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <p><strong>Lưu ý:</strong> Đối với phương thức chuyển khoản hoặc qua thẻ, có thể mất từ 1-3 ngày làm việc để số tiền nổi trong tài khoản của bạn tùy thuộc vào ngân hàng thụ hưởng.</p>
            <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
        </div>
        <div class="footer">
            Đây là email tự động, vui lòng không trả lời. Nếu bạn cần hỗ trợ, hãy liên hệ với chúng tôi qua tổng đài hoặc email hỗ trợ.
        </div>
    </div>
</body>
</html>
