<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Travel Wonder</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; background-color: #f1f5f9; padding: 40px 20px; }
        .wrapper { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; }

        /* ===== HEADER ===== */
        .header { padding: 40px 40px 28px 40px; border-bottom: 1px solid #f1f5f9; }

        /* ===== BODY ===== */
        .body { padding: 36px 40px 40px 40px; }
        .greeting { font-size: 15px; margin-bottom: 8px; color: #0f172a; }
        .greeting strong { color: #1e3a8a; font-weight: 600; }
        .intro { font-size: 14px; color: #64748b; margin-bottom: 32px; line-height: 1.7; }

        /* ===== SECURITY NOTICE ===== */
        .security-box { background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 18px 20px; margin-bottom: 28px; }
        .security-box .lock-icon { display: inline-block; margin-right: 6px; }
        .security-box h4 { font-size: 13.5px; color: #92400e; margin-bottom: 6px; font-weight: 600; }
        .security-box p { font-size: 13px; color: #78350f; line-height: 1.55; }

        /* ===== CTA BUTTON ===== */
        .cta-wrapper { text-align: center; margin: 32px 0; }
        .btn-reset { display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); color: #ffffff !important; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35); }

        /* ===== LINK FALLBACK ===== */
        .link-fallback { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px 20px; margin-bottom: 28px; }
        .link-fallback p { font-size: 12.5px; color: #64748b; margin-bottom: 8px; }
        .link-fallback .link-url { font-size: 12px; color: #2563eb; word-break: break-all; }

        /* ===== EXPIRE NOTICE ===== */
        .expire-notice { font-size: 13px; color: #64748b; text-align: center; margin-bottom: 28px; }
        .expire-notice strong { color: #dc2626; }

        /* ===== IGNORE NOTICE ===== */
        .ignore-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px 20px; font-size: 13px; color: #166534; }
        .ignore-box strong { font-weight: 600; }

        /* ===== SUPPORT ===== */
        .support-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 18px 20px; font-size: 13.5px; margin-top: 28px; color: #1e40af; }
        .support-box h4 { color: #1e3a8a; font-size: 14px; margin-bottom: 8px; font-weight: 600; }
        .support-item { margin-bottom: 4px; }
        .support-item:last-child { margin-bottom: 0; }
        .support-item .hotline { font-weight: 700; color: #2563eb; }

        /* ===== FOOTER ===== */
        .footer { background: #0f172a; color: #94a3b8; text-align: center; padding: 28px 40px; font-size: 12px; line-height: 1.8; }
        .footer .company { color: #ffffff; font-weight: 600; font-size: 13px; margin-bottom: 6px; letter-spacing: 0.3px; }
        .footer .disclaimer { margin-top: 16px; opacity: 0.5; border-top: 1px solid #334155; padding-top: 16px; font-style: italic; }
    </style>
</head>
<body>
<div class="wrapper">

{{-- ===== HEADER ===== --}}
<div style="padding: 36px 40px 10px; background: #ffffff; border-radius: 8px 8px 0 0;">
    <div style="margin-bottom: 28px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
            <tr>
                <td style="padding-right: 8px; padding-top: 2px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9l20-7z"/></svg>
                </td>
                <td style="font-size: 22px; font-weight: 500; letter-spacing: -0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    <span style="color: #0ea5e9;">Travel</span><span style="color: #334155;">Wonder</span>
                </td>
            </tr>
        </table>
    </div>
    <h1 style="font-size: 24px; color: #0f172a; margin: 0 0 6px 0; font-weight: 600; letter-spacing: -0.5px;">Đặt Lại Mật Khẩu</h1>
    <p style="font-size: 14.5px; color: #64748b; margin: 0;">Yêu cầu khôi phục mật khẩu tài khoản của bạn.</p>
</div>

{{-- ===== BODY ===== --}}
<div class="body">

    <p class="greeting">Xin chào <strong>{{ $customerName }}</strong>,</p>
    <p class="intro">
        Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản Travel Wonder được liên kết với địa chỉ email này.
        Nhấn vào nút bên dưới để tiến hành đặt lại mật khẩu của bạn.
    </p>

    {{-- ===== SECURITY NOTICE ===== --}}
    <div class="security-box">
        <h4>⚠️ Thông Báo Bảo Mật</h4>
        <p>Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email này. Tài khoản của bạn vẫn hoàn toàn an toàn và mật khẩu sẽ không bị thay đổi.</p>
    </div>

    {{-- ===== CTA BUTTON ===== --}}
    <div class="cta-wrapper">
        <a href="{{ $resetUrl }}" class="btn-reset"> Đặt Lại Mật Khẩu Ngay</a>
    </div>

    {{-- ===== EXPIRE NOTICE ===== --}}
    <p class="expire-notice">
        Liên kết này sẽ hết hạn sau <strong>{{ $expireMinutes }} phút</strong>.
    </p>

    {{-- ===== LINK FALLBACK ===== --}}
    <!-- <div class="link-fallback">
        <p>Nếu nút phía trên không hoạt động, hãy sao chép và dán URL sau vào trình duyệt của bạn:</p>
        <div class="link-url">{{ $resetUrl }}</div>
    </div> -->

    {{-- ===== IGNORE NOTICE ===== --}}
    <!-- <div class="ignore-box">
        <strong>Bạn không yêu cầu đặt lại mật khẩu?</strong> Không cần thực hiện thêm bất kỳ hành động nào. Mật khẩu của bạn sẽ không thay đổi cho đến khi bạn nhấp vào liên kết trên và tạo mật khẩu mới.
    </div> -->

    {{-- ===== THÔNG TIN HỖ TRỢ ===== --}}
    <div class="support-box">
        <h4>Cần Hỗ Trợ?</h4>
        <div class="support-item">Hotline 24/7 (miễn cước): <span class="hotline">1900 8888</span></div>
        <div class="support-item">Zalo OA: Travel Wonder Official</div>
        <div class="support-item">Email: cskh@travelwonder.com</div>
    </div>

</div>

{{-- ===== FOOTER ===== --}}
<div class="footer">
    <div class="company">CÔNG TY CỔ PHẦN DU LỊCH TRAVEL WONDER</div>
    <div>Trụ sở: Số 123 Đường Du Lịch, Quận 1, TP. Hồ Chí Minh</div>
    <div>Hotline: 1900 8888 &nbsp;|&nbsp; Email: cskh@travelwonder.com</div>
    <div>MST: 0123456789 &nbsp;|&nbsp; GPLH: 01-1234/2026/TCDL-GP</div>
    <div class="disclaimer">Email này được gửi tự động từ hệ thống. Quý khách vui lòng không trả lời trực tiếp email này.</div>
</div>

</div>
</body>
</html>
