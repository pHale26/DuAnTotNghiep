@php
    $schedule = $booking->tour_schedule;
    $tour = $schedule?->tour;
    $identity = $booking->user?->identity;
    $mainPassenger = $booking->booking_passengers
        ->firstWhere('passenger_type', 'adult')
        ?? $booking->booking_passengers->first();

    $bookingCode = 'BOOK-' . $booking->created_at->format('Ymd') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);
    $invoiceCode = 'INV-' . $booking->created_at->format('Ymd') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);

    $adultCount = (int) $booking->adults_count;
    $childCount = (int) $booking->children_count;
    $discountAmount = (float) ($booking->discount_amount ?? 0);
    $transportAmount = (float) ($booking->transport_price ?? 0);

    $addonAmount = (float) $booking->addons->sum(function ($addon) {
        return (float) $addon->pivot->price * (int) $addon->pivot->quantity;
    });

    $ticketAmount = (float) $booking->ticket_bookings->sum('total_price');
    
    $accAmount = 0;
    $accAmount = 0;
    if ($booking->booking_accommodations && $booking->booking_accommodations->isNotEmpty()) {
        $bAcc = $booking->booking_accommodations->first();
        $accAmount = $bAcc->total_amount;
    } elseif ($booking->accommodation_id && $booking->price_breakdown) {
        $bk = $booking->price_breakdown;
        $accAmount = ($bk['accommodation_base'] ?? 0) + ($bk['accommodation_single_supplement'] ?? 0) + ($bk['accommodation_extra_bed'] ?? 0) + ($bk['accommodation_child'] ?? 0);
    }
    
    $grossAmount = (float) $booking->total_price + $discountAmount;
    $tourAmount = max(0, $grossAmount - $transportAmount - $addonAmount - $ticketAmount - $accAmount);

    $normalAdultPrice = (float) ($tour?->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour?->base_price ?? 0));
    $normalChildPrice = (float) ($tour?->child_price ?? ($normalAdultPrice * 0.75));
    $normalTourAmount = ($normalAdultPrice * $adultCount) + ($normalChildPrice * $childCount);
    $priceRatio = $normalTourAmount > 0 ? ($tourAmount / $normalTourAmount) : 1;

    $adultUnitPrice = $normalAdultPrice * $priceRatio;
    $childUnitPrice = $normalChildPrice * $priceRatio;
    $adultTotal = $adultUnitPrice * $adultCount;
    $childTotal = $childUnitPrice * $childCount;

    $transportLabel = match ($booking->transport_type) {
        'flight' => 'Phương tiện máy bay',
        'bus' => 'Phương tiện xe khách / ô tô',
        default => 'Phương tiện tự túc',
    };

    $paymentMethodLabel = match ($booking->payment_method) {
        'vnpay' => 'Thanh toán VNPay',
        'transfer' => 'Chuyển khoản ngân hàng',
        default => ucfirst((string) $booking->payment_method),
    };

    $paymentStatusLabel = match ($booking->payment_status) {
        'paid_100' => 'Đã thanh toán 100%',
        'paid_30' => 'Đã thanh toán 30%',
        'failed' => 'Thanh toán thất bại',
        default => 'Chờ thanh toán',
    };

    $paymentStatusClass = match ($booking->payment_status) {
        'paid_100' => 'status-success',
        'paid_30' => 'status-info',
        'failed' => 'status-danger',
        default => 'status-warning',
    };

    $isPaid = in_array($booking->payment_status, ['paid_30', 'paid_100'], true);
    $transactionCode = $payment?->transaction_code ?? ($isPaid ? 'CK-' . $bookingCode : 'Chưa có');
    $paidAt = $payment?->paid_at ?? ($isPaid ? $booking->updated_at : null);

    $customerName = $identity?->full_name
        ?? $mainPassenger?->full_name
        ?? $booking->user?->name
        ?? 'Chưa cập nhật';

    $customerIdentity = $identity?->identity_number
        ?? $mainPassenger?->identity_number
        ?? 'Chưa cập nhật';

    $customerAddress = $identity?->place_of_residence
        ?? $identity?->place_of_origin
        ?? 'Chưa cập nhật';

    $primaryImage = $tour?->tour_images?->firstWhere('is_primary', true)
        ?? $tour?->tour_images?->first();

    /*
    |--------------------------------------------------------------------------
    | Xử lý ảnh tour cho cả trang web và file PDF
    |--------------------------------------------------------------------------
    | Hỗ trợ các kiểu đường dẫn thường gặp:
    | - tours/ten-anh.jpg
    | - storage/tours/ten-anh.jpg
    | - public/storage/tours/ten-anh.jpg
    | - storage/app/public/tours/ten-anh.jpg
    | - uploads/tours/ten-anh.jpg
    | - URL http/https
    */
    $tourImageSrc = null;
    $rawImage = null;
    $isPdfMode = (bool) ($isPdf ?? false);

    if ($primaryImage) {
        foreach (['image_url', 'image_path', 'path', 'image'] as $imageColumn) {
            $candidateValue = $primaryImage->getRawOriginal($imageColumn);

            if (!empty($candidateValue)) {
                $rawImage = trim(str_replace('\\', '/', (string) $candidateValue));
                break;
            }
        }
    }

    if ($rawImage) {
        if (
            str_starts_with($rawImage, 'http://')
            || str_starts_with($rawImage, 'https://')
        ) {
            // URL trực tuyến. DomPDF đọc được khi Controller bật isRemoteEnabled.
            $tourImageSrc = $rawImage;
        } else {
            $cleanPath = ltrim($rawImage, '/');

            // Loại bỏ các tiền tố tuyệt đối/tương đối thường bị lưu thừa.
            $cleanPath = preg_replace(
                '#^(public/|storage/app/public/)#',
                '',
                $cleanPath
            );

            $storageRelativePath = preg_replace(
                '#^storage/#',
                '',
                $cleanPath
            );

            $imageCandidates = [
                [
                    'file' => storage_path('app/public/' . $storageRelativePath),
                    'url' => asset('storage/' . $storageRelativePath),
                ],
                [
                    'file' => public_path('storage/' . $storageRelativePath),
                    'url' => asset('storage/' . $storageRelativePath),
                ],
                [
                    'file' => public_path($cleanPath),
                    'url' => asset($cleanPath),
                ],
            ];

            foreach ($imageCandidates as $imageCandidate) {
                $imageFile = $imageCandidate['file'];

                if (!is_file($imageFile)) {
                    continue;
                }

                if ($isPdfMode) {
                    /*
                    |------------------------------------------------------------------
                    | Chuyển ảnh local sang base64 để DomPDF đọc ổn định trên Windows
                    |------------------------------------------------------------------
                    */
                    $extension = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
                    $mimeType = match ($extension) {
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml',
                        default => 'image/jpeg',
                    };

                    $imageBinary = @file_get_contents($imageFile);

                    if ($imageBinary !== false) {
                        $tourImageSrc = 'data:'
                            . $mimeType
                            . ';base64,'
                            . base64_encode($imageBinary);
                    }
                } else {
                    $tourImageSrc = $imageCandidate['url'];
                }

                break;
            }

            /*
            |--------------------------------------------------------------------------
            | Dự phòng cho accessor image_url của Model TourImage
            |--------------------------------------------------------------------------
            */
            if (!$tourImageSrc && !$isPdfMode) {
                $accessorImage = $primaryImage?->image_url;

                if (!empty($accessorImage)) {
                    $tourImageSrc = $accessorImage;
                }
            }
        }
    }
@endphp

<style>
    .invoice-wrap {
        font-family: DejaVu Sans, Arial, sans-serif;
        color: #1e293b;
        font-size: 13px;
    }

    .invoice-sheet {
        max-width: 1050px;
        margin: 0 auto;
        padding: 26px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-top td,
    .info-columns td,
    .tour-info td,
    .payment-columns td {
        vertical-align: top;
    }

    .brand-logo {
        width: 105px;
        height: 72px;
        margin-bottom: 4px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0369a1;
        text-align: center;
        line-height: 72px;
        font-size: 37px;
        font-weight: 800;
    }

    .brand-name {
        color: #0f3c6e;
        font-size: 23px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .brand-slogan {
        color: #64748b;
        font-size: 11px;
    }

    .invoice-title {
        margin: 4px 0 0;
        font-size: 25px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
    }

    .invoice-subtitle {
        text-align: center;
        font-size: 14px;
        font-weight: 700;
    }

    .invoice-code {
        margin-top: 11px;
        color: #dc2626;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
    }

    .invoice-date {
        margin-top: 8px;
        text-align: center;
    }

    /* .qr-box {
        width: 92px;
        height: 92px;
        margin-left: auto;
        border: 7px solid #fff;
        outline: 1px solid #cbd5e1;
        background:
            conic-gradient(#111 25%, #fff 0 50%, #111 0 75%, #fff 0) 0 0 / 12px 12px;
        position: relative;
    }

    .qr-box span {
        position: absolute;
        left: 16px;
        right: 16px;
        top: 37px;
        padding: 3px 0;
        background: #fff;
        color: #111;
        text-align: center;
        font-size: 10px;
        font-weight: 800;
    } */

    .divider {
        border-top: 1px solid #e2e8f0;
        margin: 20px 0;
    }

    .section-title {
        margin-bottom: 11px;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .info-columns td:first-child,
    .payment-columns td:first-child {
        width: 50%;
        padding-right: 28px;
    }

    .info-columns td:last-child,
    .payment-columns td:last-child {
        width: 50%;
        padding-left: 28px;
    }

    .detail-row {
        margin-bottom: 7px;
        line-height: 1.55;
    }

    .detail-label {
        display: inline-block;
        min-width: 112px;
        font-weight: 700;
        color: #0f172a;
    }

    .detail-value {
        color: #475569;
    }

    .tour-image-cell {
        width: 240px;
        padding-right: 20px;
    }

    .tour-image {
        width: 220px;
        height: 142px;
        display: block;
        object-fit: cover;
        object-position: center;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
    }

    .tour-image-empty {
        width: 220px;
        height: 142px;
        border-radius: 7px;
        border: 1px dashed #94a3b8;
        background: #f1f5f9;
        color: #64748b;
        text-align: center;
        line-height: 142px;
    }

    .items-table {
        width: 100%;
        margin-top: 18px;
        border-collapse: collapse;
        border: 1px solid #dbe2ea;
    }

    .items-table th {
        background: #f8fafc;
        padding: 9px 8px;
        border: 1px solid #dbe2ea;
        font-size: 11px;
        text-transform: uppercase;
    }

    .items-table td {
        padding: 9px 8px;
        border: 1px solid #dbe2ea;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-danger { color: #dc2626; }

    .total-box {
        width: 43%;
        margin: 14px 0 0 auto;
    }

    .total-box td {
        padding: 7px 3px;
        border-bottom: 1px solid #e2e8f0;
    }

    .total-box td:last-child {
        text-align: right;
        font-weight: 700;
    }

    .grand-total td {
        padding-top: 11px;
        color: #0756b8;
        font-size: 18px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .payment-box {
        margin-top: 20px;
        padding: 17px;
        border: 1px solid #dbe2ea;
        border-radius: 7px;
    }

    .status-pill {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-success { background: #dcfce7; color: #15803d; }
    .status-info { background: #e0f2fe; color: #0369a1; }
    .status-warning { background: #fef3c7; color: #a16207; }
    .status-danger { background: #fee2e2; color: #b91c1c; }

    .signature {
        margin-top: 28px;
        text-align: center;
    }

    .signature-company {
        margin-top: 4px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .signature-note {
        font-size: 11px;
        font-style: italic;
    }

    .signature-name {
        margin-top: 52px;
        font-weight: 800;
    }

    .invoice-bottom-note {
        margin-top: 15px;
        color: #64748b;
        font-size: 10px;
    }

    @media print {
        @page { size: A4 portrait; margin: 8mm; }

        body { background: #fff !important; }
        .sidebar,
        .topbar,
        .invoice-toolbar,
        .main-content > .text-center {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-sheet {
            max-width: none;
            padding: 0;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }
    }
    .tour-info {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.tour-info > tbody > tr > td {
    vertical-align: top;
}

.tour-image-cell {
    width: 185px;
    padding-right: 18px;
    vertical-align: top;
}

.tour-image {
    width: 165px;
    height: 108px;
    display: block;
    object-fit: cover;
    object-position: center;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

.tour-image-empty {
    width: 165px;
    height: 108px;
    background: #f1f5f9;
    border: 1px dashed #94a3b8;
    border-radius: 6px;
    color: #64748b;
    text-align: center;
    line-height: 108px;
}

.tour-detail-cell {
    padding-left: 5px;
    vertical-align: top;
}

.tour-detail-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.tour-detail-table td {
    padding: 3px 0;
    vertical-align: top;
    line-height: 1.35;
}

.tour-detail-label {
    width: 115px;
    padding-right: 10px !important;
    color: #0f172a;
    font-weight: 700;
    white-space: nowrap;
}

.tour-detail-value {
    color: #475569;
    word-wrap: break-word;
    overflow-wrap: break-word;
}
.invoice-qr-code {
    width: 110px;
    height: 110px;
    display: block;
    margin-left: auto;
    border: 1px solid #dbe2ea;
    padding: 4px;
    background: #fff;
}
</style>

<div class="invoice-wrap">
    <div class="invoice-sheet" id="invoice-print-area">
        <table class="invoice-table invoice-top">
            <tr>
                <td style="width: 26%;">
                    <div class="brand-logo">✈</div>
                    <div class="brand-name">TRAVEL</div>
                    <div class="brand-slogan">Khám phá thế giới cùng bạn</div>
                </td>

                <td style="width: 50%;">
                    <h1 class="invoice-title">Hóa đơn thanh toán</h1>
                    <div class="invoice-subtitle">(INVOICE)</div>
                    <div class="invoice-code">Mã hóa đơn: {{ $invoiceCode }}</div>
                    <div class="invoice-date">Ngày xuất hóa đơn: {{ now()->format('d/m/Y') }}</div>
                </td>

                <td style="width: 24%; text-align: right;">
    @if(!empty($qrCode))
        <img
            src="{{ $qrCode }}"
            alt="QR hóa đơn"
            class="invoice-qr-code"
        >
    @endif
</td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="invoice-table info-columns">
            <tr>
                <td>
                    <div class="section-title">Thông tin đơn vị cung cấp</div>
                    <div class="detail-row"><span class="detail-label">Công ty:</span><span class="detail-value">Travel Wonder</span></div>
                    <div class="detail-row"><span class="detail-label">Địa chỉ:</span><span class="detail-value">Thanh Hóa, Việt Nam</span></div>
                    <div class="detail-row"><span class="detail-label">MST:</span><span class="detail-value">0312345678</span></div>
                    <div class="detail-row"><span class="detail-label">Hotline:</span><span class="detail-value">1900 1234</span></div>
                    <div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value">info@travelwonder.vn</span></div>
                    <div class="detail-row"><span class="detail-label">Website:</span><span class="detail-value">travelwonder.vn</span></div>
                </td>

                <td>
                    <div class="section-title">Thông tin khách hàng</div>
                    <div class="detail-row"><span class="detail-label">Họ và tên:</span><span class="detail-value">{{ $customerName }}</span></div>
                    <div class="detail-row"><span class="detail-label">Số điện thoại:</span><span class="detail-value">{{ $booking->user?->phone ?? 'Chưa cập nhật' }}</span></div>
                    <div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value">{{ $booking->user?->email ?? 'Chưa cập nhật' }}</span></div>
                    <div class="detail-row"><span class="detail-label">Địa chỉ:</span><span class="detail-value">{{ $customerAddress }}</span></div>
                    <div class="detail-row"><span class="detail-label">CCCD/Hộ chiếu:</span><span class="detail-value">{{ $customerIdentity }}</span></div>
                    <div class="detail-row"><span class="detail-label">Ngày đặt:</span><span class="detail-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span></div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="section-title">Thông tin tour</div>

<table class="tour-info">
    <tr>
        <td class="tour-image-cell">
            @if($tourImageSrc)
                <img
                    src="{{ $tourImageSrc }}"
                    alt="Ảnh tour"
                    class="tour-image"
                >
            @else
                <div class="tour-image-empty">
                    Chưa có ảnh tour
                </div>
            @endif
        </td>

        <td class="tour-detail-cell">
            <table class="tour-detail-table">
                <tr>
                    <td class="tour-detail-label">Tên tour:</td>
                    <td class="tour-detail-value">
                        <strong>
                            {{ $tour?->title ?? 'Chưa cập nhật' }}
                        </strong>
                    </td>
                </tr>

                <tr>
                    <td class="tour-detail-label">Mã tour:</td>
                    <td class="tour-detail-value">
                        {{ $tour?->code ?? 'TOUR-' . ($tour?->id ?? '') }}
                    </td>
                </tr>

                <tr>
                    <td class="tour-detail-label">Mã đơn:</td>
                    <td class="tour-detail-value">
                        {{ $bookingCode }}
                    </td>
                </tr>

                <tr>
                    <td class="tour-detail-label">Lịch khởi hành:</td>
                    <td class="tour-detail-value">
                        {{ $schedule?->departure_date?->format('d/m/Y') ?? 'Chưa cập nhật' }}
                    </td>
                </tr>

                <tr>
                    <td class="tour-detail-label">Số lượng khách:</td>
                    <td class="tour-detail-value">
                        Người lớn: {{ $adultCount }}
                        &nbsp;|&nbsp;
                        Trẻ em: {{ $childCount }}
                    </td>
                </tr>

                <tr>
                    <td class="tour-detail-label">Điểm tập kết:</td>
                    <td class="tour-detail-value">
                        {{ $booking->meeting_point ?? $tour?->meeting_point ?? 'Chưa cập nhật' }}
                    </td>
                </tr>

                @if($booking->booking_accommodations && $booking->booking_accommodations->isNotEmpty())
                @php $bAcc = $booking->booking_accommodations->first(); @endphp
                <tr>
                    <td class="tour-detail-label">Hạng lưu trú:</td>
                    <td class="tour-detail-value">
                        <strong>{{ $bAcc->accommodation_name_snapshot }}</strong><br>
                        <small class="text-muted">Hạng phòng: {{ $bAcc->room_type_name_snapshot }}</small>
                        <br><small class="text-muted">(Phòng: {{ $bAcc->single_rooms_count }}, Giường phụ: {{ $bAcc->extra_bed_qty }})</small>
                    </td>
                </tr>
                @elseif($booking->accommodation_id && $booking->accommodation)
                <tr>
                    <td class="tour-detail-label">Hạng lưu trú:</td>
                    <td class="tour-detail-value">
                        <strong>{{ $booking->accommodation->name }}</strong><br>
                        <small class="text-muted">{{ $booking->accommodation->address }}</small>
                        @if($booking->single_rooms_count > 0 || $booking->extra_beds_count > 0)
                            <br><small class="text-muted">(Phòng đơn: {{ $booking->single_rooms_count }}, Giường phụ: {{ $booking->extra_beds_count }})</small>
                        @endif
                    </td>
                </tr>
                @endif

                <tr>
                    <td class="tour-detail-label">Phương tiện:</td>
                    <td class="tour-detail-value">
                        {{ $transportLabel }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 7%;" class="text-center">STT</th>
                    <th>Nội dung</th>
                    <th style="width: 19%;" class="text-right">Đơn giá</th>
                    <th style="width: 13%;" class="text-center">Số lượng</th>
                    <th style="width: 20%;" class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $stt = 1; @endphp

                @if($adultCount > 0)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>Giá tour người lớn</td>
                        <td class="text-right">{{ number_format($adultUnitPrice, 0, ',', '.') }} ₫</td>
                        <td class="text-center">{{ $adultCount }}</td>
                        <td class="text-right">{{ number_format($adultTotal, 0, ',', '.') }} ₫</td>
                    </tr>
                @endif

                @if($childCount > 0)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>Giá tour trẻ em</td>
                        <td class="text-right">{{ number_format($childUnitPrice, 0, ',', '.') }} ₫</td>
                        <td class="text-center">{{ $childCount }}</td>
                        <td class="text-right">{{ number_format($childTotal, 0, ',', '.') }} ₫</td>
                    </tr>
                @endif

                @if($booking->booking_accommodations && $booking->booking_accommodations->isNotEmpty())
                    @php 
                        $bAcc = $booking->booking_accommodations->first();
                        $accTotal = $bAcc->total_amount;
                    @endphp
                    @if($accTotal > 0)
                        <tr>
                            <td class="text-center">{{ $stt++ }}</td>
                            <td>Phí lưu trú ({{ $bAcc->accommodation_name_snapshot }} - {{ $bAcc->room_type_name_snapshot }})</td>
                            <td class="text-right"></td>
                            <td class="text-center">1</td>
                            <td class="text-right">{{ number_format($accTotal, 0, ',', '.') }} ₫</td>
                        </tr>
                    @endif
                @elseif($booking->accommodation_id && $booking->price_breakdown)
                    @php 
                        $bk = $booking->price_breakdown; 
                        $accTotal = ($bk['accommodation_base'] ?? 0) + ($bk['accommodation_single_supplement'] ?? 0) + ($bk['accommodation_extra_bed'] ?? 0) + ($bk['accommodation_child'] ?? 0);
                    @endphp
                    @if($accTotal > 0)
                        <tr>
                            <td class="text-center">{{ $stt++ }}</td>
                            <td>Phí lưu trú ({{ $booking->accommodation->name }})</td>
                            <td class="text-right"></td>
                            <td class="text-center">1</td>
                            <td class="text-right">{{ number_format($accTotal, 0, ',', '.') }} ₫</td>
                        </tr>
                    @endif
                @endif

                @if($transportAmount > 0)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $transportLabel }}</td>
                        <td class="text-right">{{ number_format($transportAmount, 0, ',', '.') }} ₫</td>
                        <td class="text-center">1</td>
                        <td class="text-right">{{ number_format($transportAmount, 0, ',', '.') }} ₫</td>
                    </tr>
                @endif

                @foreach($booking->ticket_bookings as $ticketBooking)
                    @php
                        $ticketQty = max(1, (int) $ticketBooking->quantity);
                        $ticketUnitPrice = (float) $ticketBooking->total_price / $ticketQty;
                        $ticketName = $ticketBooking->ticket_option?->ticket?->title ?? 'Vé tham quan';
                        $optionName = $ticketBooking->ticket_option?->name;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $ticketName }}{{ $optionName ? ' - ' . $optionName : '' }}</td>
                        <td class="text-right">{{ number_format($ticketUnitPrice, 0, ',', '.') }} ₫</td>
                        <td class="text-center">{{ $ticketQty }}</td>
                        <td class="text-right">{{ number_format($ticketBooking->total_price, 0, ',', '.') }} ₫</td>
                    </tr>
                @endforeach

                @foreach($booking->addons as $addon)
                    @php
                        $addonQty = max(1, (int) $addon->pivot->quantity);
                        $addonUnitPrice = (float) $addon->pivot->price;
                        $addonLineTotal = $addonUnitPrice * $addonQty;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $addon->pivot->addon_name ?? $addon->name }}</td>
                        <td class="text-right">{{ number_format($addonUnitPrice, 0, ',', '.') }} ₫</td>
                        <td class="text-center">{{ $addonQty }}</td>
                        <td class="text-right">{{ number_format($addonLineTotal, 0, ',', '.') }} ₫</td>
                    </tr>
                @endforeach

                @if($discountAmount > 0)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>Giảm giá{{ $booking->coupon?->code ? ' (Mã: ' . $booking->coupon->code . ')' : '' }}</td>
                        <td class="text-right">-</td>
                        <td class="text-center">1</td>
                        <td class="text-right text-danger">-{{ number_format($discountAmount, 0, ',', '.') }} ₫</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="total-box">
            <table class="invoice-table">
                <tr>
                    <td>Tạm tính:</td>
                    <td>{{ number_format($grossAmount, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <td>Giảm giá:</td>
                    <td class="text-danger">-{{ number_format($discountAmount, 0, ',', '.') }} ₫</td>
                </tr>
                <tr class="grand-total">
                    <td>Tổng thanh toán:</td>
                    <td>{{ number_format($booking->total_price, 0, ',', '.') }} ₫</td>
                </tr>
            </table>
        </div>

        <div class="payment-box">
            <table class="invoice-table payment-columns">
                <tr>
                    <td>
                        <div class="section-title">Phương thức thanh toán</div>
                        <div class="detail-row"><span class="detail-label">Thanh toán:</span><span class="detail-value">{{ $paymentMethodLabel }}</span></div>
                        <div class="detail-row"><span class="detail-label">Mã giao dịch:</span><span class="detail-value">{{ $transactionCode }}</span></div>
                        <div class="detail-row"><span class="detail-label">Ngày thanh toán:</span><span class="detail-value">{{ $paidAt?->format('d/m/Y H:i') ?? 'Chưa thanh toán' }}</span></div>
                        <div class="detail-row"><span class="detail-label">Đã thanh toán:</span><span class="detail-value">{{ number_format($booking->paid_amount ?? 0, 0, ',', '.') }} ₫</span></div>
                        <div class="detail-row"><span class="detail-label">Trạng thái:</span><span class="status-pill {{ $paymentStatusClass }}">{{ $paymentStatusLabel }}</span></div>
                    </td>

                    <td>
                        <div class="section-title">Ghi chú</div>
                        <div class="detail-row">Cảm ơn quý khách đã đặt tour tại Travel Wonder!</div>
                        <div class="detail-row">Mọi thắc mắc vui lòng liên hệ hotline <strong>1900 1234</strong> để được hỗ trợ.</div>

                        <div class="signature">
                            <div>Hải Phòng, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</div>
                            <div class="signature-company">Đại diện công ty</div>
                            <div class="signature-note">(Ký, ghi rõ họ tên)</div>
                            <div class="signature-name">TRAVEL WONDER</div>
                            <div>Giám đốc</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-bottom-note">Lưu ý: Hóa đơn này được tạo tự động, không cần ký và đóng dấu.</div>
    </div>
</div>
