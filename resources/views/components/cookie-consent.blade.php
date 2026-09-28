@php
    $consentCookie = request()->cookie('cookie_consent');
    $shouldShow = $consentCookie === null;
@endphp

@if ($shouldShow)
<div id="cookie-consent-banner" role="dialog" aria-modal="true" aria-label="Thông báo Cookie">
    <style>
        #cookie-consent-banner {
            position: fixed;
            bottom: 28px;
            left: 28px;
            z-index: 99999;
            width: 340px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18), 0 2px 12px rgba(0,0,0,0.08);
            padding: 28px 28px 22px 28px;
            animation: cookieBannerSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
            border: 1px solid rgba(0,0,0,0.06);
        }

        @keyframes cookieBannerSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        #cookie-consent-banner .cookie-close-btn {
            position: absolute;
            top: 14px;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: #aaa;
            font-size: 18px;
            line-height: 1;
            padding: 4px;
            transition: color 0.2s;
        }

        #cookie-consent-banner .cookie-close-btn:hover {
            color: #555;
        }

        #cookie-consent-banner .cookie-icon {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
        }

        #cookie-consent-banner .cookie-body {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 20px;
        }

        #cookie-consent-banner .cookie-text h6 {
            font-family: 'Inter', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 6px 0;
            line-height: 1.3;
        }

        #cookie-consent-banner .cookie-text p {
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            color: #777;
            margin: 0;
            line-height: 1.55;
        }

        #cookie-consent-banner .cookie-actions {
            display: flex;
            gap: 10px;
        }

        #cookie-consent-banner .cookie-btn-decline {
            flex: 1;
            padding: 10px 0;
            border-radius: 50px;
            border: 2px solid #e0e0e0;
            background: #fff;
            color: #555;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        #cookie-consent-banner .cookie-btn-decline:hover {
            border-color: #007CE8;
            color: #007CE8;
            background: rgba(0, 124, 232, 0.04);
        }

        #cookie-consent-banner .cookie-btn-accept {
            flex: 1;
            padding: 10px 0;
            border-radius: 50px;
            border: none;
            background: #007CE8;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 124, 232, 0.35);
        }

        #cookie-consent-banner .cookie-btn-accept:hover {
            background: #005bb5;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 124, 232, 0.45);
        }

        #cookie-consent-banner .cookie-btn-accept:active,
        #cookie-consent-banner .cookie-btn-decline:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            #cookie-consent-banner {
                left: 12px;
                right: 12px;
                bottom: 16px;
                width: auto;
            }
        }
    </style>

    {{-- Nút đóng (chỉ ẩn banner tạm thời, không lưu consent) --}}
    <button class="cookie-close-btn" onclick="document.getElementById('cookie-consent-banner').style.display='none'" aria-label="Đóng">
        &times;
    </button>

    <div class="cookie-body">
        {{-- Cookie SVG Icon --}}
        <svg class="cookie-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="48" fill="#D4722A"/>
            <circle cx="50" cy="50" r="44" fill="#E8913F"/>
            {{-- Chip vân cookie --}}
            <ellipse cx="35" cy="38" rx="7" ry="7" fill="#7B3B0A"/>
            <ellipse cx="58" cy="32" rx="5" ry="5" fill="#7B3B0A"/>
            <ellipse cx="68" cy="55" rx="6" ry="6" fill="#7B3B0A"/>
            <ellipse cx="42" cy="65" rx="8" ry="7" fill="#7B3B0A"/>
            <ellipse cx="62" cy="70" rx="5" ry="5" fill="#7B3B0A"/>
            <ellipse cx="30" cy="58" rx="4" ry="4" fill="#7B3B0A"/>
            {{-- Highlight --}}
            <ellipse cx="38" cy="28" rx="8" ry="5" fill="rgba(255,255,255,0.15)" transform="rotate(-30 38 28)"/>
        </svg>

        <div class="cookie-text">
            <h6>{{ __('Chúng tôi dùng Cookie') }}</h6>
            <p>{{ __('Trang web sử dụng cookie để lưu tùy chọn ngôn ngữ và tiền tệ của bạn. Nếu từ chối, dữ liệu chỉ được lưu trong phiên làm việc hiện tại.') }}</p>
        </div>
    </div>

    <div class="cookie-actions">
        {{-- Từ chối --}}
        <form method="POST" action="{{ route('cookie.consent.decline') }}" style="flex:1;">
            @csrf
            <button type="submit" class="cookie-btn-decline w-100">{{ __('Từ chối') }}</button>
        </form>

        {{-- Đồng ý --}}
        <form method="POST" action="{{ route('cookie.consent.accept') }}" style="flex:1;">
            @csrf
            <button type="submit" class="cookie-btn-accept w-100">{{ __('Đồng ý') }}</button>
        </form>
    </div>
</div>
@endif
