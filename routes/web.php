<?php

use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Frontend\InvoiceRequestController;
use App\Http\Controllers\GuestBroadcastingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\NotificationController;
use App\Models\Category;
use App\Models\Holiday;
use App\Models\TourSchedule;
use Database\Seeders\VietnamHolidaySeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/debug-schema', function () {
    $columns = Schema::getColumnListing('tours');

    return implode(', ', $columns);
});

// Chatbot routes
use App\Http\Controllers\Frontend\ChatbotController;

Route::get('/api/chatbot/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
Route::post('/api/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');

// Route tạm để seed ngày lễ (chạy 1 lần rồi có thể xóa)
Route::get('/seed-holidays', function () {
    (new VietnamHolidaySeeder)->run();

    return response()->json(['status' => 'ok', 'message' => '✅ Đã seed ngày lễ Việt Nam thành công!', 'count' => Holiday::count()]);
});

// Route debug lọc danh mục
Route::get('/debug-calendar', function (Request $request) {
    $categoryId = $request->get('cat', null);

    // Xem danh sách categories và số tour có schedule
    $cats = Category::withCount(['tours as tour_count', 'tours as schedule_count' => function ($q) {
        $q->whereHas('tour_schedules');
    }])->get(['id', 'name']);

    // Test query filter
    $query = TourSchedule::with(['tour.categories'])
        ->whereBetween('departure_date', [now()->startOfMonth(), now()->endOfMonth()])
        ->whereIn('status', ['available', 'full']);

    if ($categoryId) {
        $query->whereHas('tour.categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    $results = $query->get()->map(fn ($s) => [
        'schedule_id' => $s->id,
        'tour' => $s->tour?->title,
        'categories' => $s->tour?->categories->pluck('name'),
    ]);

    return response()->json([
        'all_categories' => $cats,
        'filtered_schedules' => $results,
        'filter_cat_id' => $categoryId,
    ]);
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tour-tron-goi', [HomeController::class, 'tours'])->name('frontend.tours.index');
Route::get('/tours/search', function (Request $request) {
    return redirect()->route('frontend.tours.index', $request->query());
})->name('frontend.tours.search');
Route::get('/tours/ai-suggest', [HomeController::class, 'aiSuggest'])->name('frontend.tours.ai_suggest');
Route::get('/api/destinations/search', [HomeController::class, 'searchDestinations'])->name('api.destinations.search');
Route::get('/api/provinces/{province}/wards', [LocationController::class, 'getWards'])->name('api.provinces.wards');

use App\Http\Controllers\Admin\AccommodationController;
// Frontend Controllers
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\OngoingTourController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TourActivityController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\TourGuideController;
use App\Http\Controllers\Admin\TourItineraryController;
use App\Http\Controllers\Api\AiTranslationController;
use App\Http\Controllers\AppSettingsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\Frontend\BookingPassengerController;
use App\Http\Controllers\Frontend\CalendarController;
use App\Http\Controllers\Frontend\FavoriteController;
use App\Http\Controllers\Frontend\FlightController;
use App\Http\Controllers\Frontend\OcrController;
use App\Http\Controllers\Frontend\SupportController;
use App\Http\Controllers\Frontend\TicketController;
use App\Http\Controllers\Frontend\TourBookingController;
use App\Http\Controllers\Frontend\TourController as FrontendTourController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Guide\AbsenceRequestController;
use App\Http\Controllers\Guide\GroupSplitController;
use App\Http\Controllers\Guide\ScheduleController;
use App\Http\Controllers\Guide\TourReportController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

Route::get('/locale/{locale}', [AppSettingsController::class, 'switchLocale'])
    ->name('locale.switch');

Route::get('/currency/{currency}', [AppSettingsController::class, 'switchCurrency'])
    ->name('currency.switch');

Route::post('/cookie-consent/accept', [CookieConsentController::class, 'accept'])
    ->name('cookie.consent.accept');

Route::post('/cookie-consent/decline', [CookieConsentController::class, 'decline'])
    ->name('cookie.consent.decline');

// VNPay Callbacks
Route::get('/tours/vnpay-return', [TourBookingController::class, 'vnpayReturn'])
    ->name('frontend.tours.vnpay_return');
Route::get('/tours/vnpay-ipn', [TourBookingController::class, 'vnpayIpn'])
    ->name('frontend.tours.vnpay_ipn');

// Tóm tắt AI
Route::get('/tours/{id}/ai-summary', [FrontendTourController::class, 'aiSummary'])
    ->name('frontend.tours.ai_summary');

// Lịch Tour
Route::get('/lich-tour', [CalendarController::class, 'index'])->name('frontend.tours.calendar');
Route::get('/api/lich-tour/data', [CalendarController::class, 'data'])->name('frontend.tours.calendar.data');

// Tìm chuyến bay
Route::get('/flights', [FlightController::class, 'search'])
    ->name('frontend.flights.search');
Route::get('/api/flights/search', [FlightController::class, 'searchApi'])
    ->name('api.flights.search');

/*
|--------------------------------------------------------------------------
| Tickets Routes
|--------------------------------------------------------------------------
*/

// Route::get('/tickets', [TicketController::class, 'index'])->name('frontend.tickets.index');
// Route::get('/tickets/search', [TicketController::class, 'search'])->name('frontend.tickets.search');

// Ticket Booking Routes removed - tickets can only be booked along with tours
// Ticket detail - MUST be last to avoid conflicts
// Route::get('/tickets/{slug}', [TicketController::class, 'show'])->name('frontend.tickets.show');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::post('/api/coupons/apply', [TourBookingController::class, 'applyCoupon'])
    ->name('coupons.apply');

// Đặt Tour
Route::get('/tours/checkout', [TourBookingController::class, 'checkout'])
    ->name('frontend.tours.checkout');

Route::post('/tours/book', [TourBookingController::class, 'store'])
    ->name('frontend.tours.store');

Route::get('/tours/booking-success/{id}', [TourBookingController::class, 'bookingSuccess'])
    ->name('frontend.tours.booking_success');

Route::get('/tours/booking/{id}/verify-email', [TourBookingController::class, 'verifyEmail'])
    ->name('frontend.tours.verify_email');
Route::post('/tours/booking/{id}/verify-email', [TourBookingController::class, 'submitVerifyEmail']);

Route::get('/tours/booking-status/{id}', [TourBookingController::class, 'checkStatus'])
    ->name('frontend.tours.booking_status');

Route::post('/tours/booking-success/{id}/create-account', [TourBookingController::class, 'createAccount'])
    ->name('frontend.tours.create_account');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Frontend
|--------------------------------------------------------------------------
*/

// Demo Bar Routes (Public cho phép Guest dùng)
Route::post('/demo/bookings/{id}/simulate-payment', [DemoController::class, 'simulatePayment'])
    ->name('demo.bookings.simulate_payment');

Route::post('/demo/bookings/{id}/fast-forward-cancel', [DemoController::class, 'fastForwardCancel'])
    ->name('demo.bookings.fast_forward_cancel');

Route::middleware(['auth'])->group(function () {
    // OCR CCCD
    Route::post('/api/scan-cccd', [OcrController::class, 'scanCccd'])
        ->name('ocr.scan-cccd');

    // Đặt vé máy bay
    Route::get('/flights/checkout', [FlightController::class, 'checkout'])
        ->name('frontend.flights.checkout');

    Route::post('/flights/book', [FlightController::class, 'book'])
        ->name('frontend.flights.book');

    // Thông báo
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_as_read');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark_as_read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread_count');

    // Broadcast Auth cho Guest
    Route::post('/broadcasting/guest-auth', [GuestBroadcastingController::class, 'auth'])->withoutMiddleware(['auth']);

    // Tài khoản user
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/user/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/user/avatar', [UserController::class, 'updateAvatar'])->name('user.avatar.update');
    Route::post('/user/password', [UserController::class, 'changePassword'])->name('user.password.change');

    Route::get('/my-bookings', fn () => redirect()->route('user.profile', ['tab' => 'bookings']))->name('user.bookings');
    Route::get('/my-bookings/{id}', [UserController::class, 'bookingDetail'])->name('user.bookings.detail');
    Route::post('/my-bookings/{id}/cancel', [UserController::class, 'cancelBooking'])->name('user.bookings.cancel');
    Route::post('/my-bookings/{id}/link', [UserController::class, 'handleBookingLink'])->name('frontend.user.bookings.link');

    Route::post('/reviews', [UserController::class, 'storeReview'])->name('user.reviews.store');

    Route::get('/my-wishlists', fn () => redirect()->route('user.profile', ['tab' => 'wishlists']))->name('user.wishlists');
    Route::post('/wishlists/toggle', [UserController::class, 'toggleWishlist'])->name('user.wishlists.toggle');
    Route::post('/wishlists/remove', [UserController::class, 'removeWishlist'])->name('user.wishlists.remove');

    // Thanh toán lại bằng VNPay
    Route::get('/bookings/{id}/pay-vnpay', [TourBookingController::class, 'payWithVNPay'])
        ->name('frontend.bookings.pay_vnpay');

    // Bổ sung danh sách hành khách
    Route::get('/bookings/{id}/passengers', [BookingPassengerController::class, 'index'])->name('frontend.bookings.passengers');
    Route::post('/bookings/{id}/passengers/manual', [BookingPassengerController::class, 'storeManual'])->name('frontend.bookings.passengers.manual');
    Route::get('/bookings/passengers/template', [BookingPassengerController::class, 'downloadTemplate'])->name('frontend.bookings.passengers.template');
    Route::post('/bookings/{id}/passengers/import', [BookingPassengerController::class, 'importExcel'])->name('frontend.bookings.passengers.import');
});

// Điểm đến
Route::get('/destinations', [App\Http\Controllers\Frontend\DestinationController::class, 'index'])
    ->name('frontend.destinations.index');

// Trung tâm hỗ trợ
Route::get('/support', [SupportController::class, 'index'])
    ->name('frontend.support.index');

// Chi tiết Tour
Route::get('/tours/{slug}', [FrontendTourController::class, 'show'])
    ->name('frontend.tours.show');
Route::middleware('auth')->group(function () {

    Route::post('/tours/{tour}/favorite', [FavoriteController::class, 'toggle'])
        ->name('frontend.favorites.toggle');

    Route::delete('/tours/{tour}/favorite', [FavoriteController::class, 'destroy'])
        ->name('frontend.favorites.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // AI Translation
    Route::post('/ai-translate', [AiTranslationController::class, 'translate'])
        ->name('admin.ai-translate');

    // User Management
    Route::get('/chat', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chat.index');

    Route::get('/refunds', [RefundController::class, 'index'])->name('admin.refunds.index');
    Route::post('/refunds/{id}/process', [RefundController::class, 'process'])->name('admin.refunds.process');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)
        ->names('admin.users');
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])
        ->name('admin.users.toggle-status');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/reviews/{id}/toggle-hidden', [ReviewController::class, 'toggleHidden'])->name('admin.reviews.toggle-hidden');

    // Booking
    Route::get('/bookings', [BookingController::class, 'index'])
        ->name('admin.bookings.index');

    Route::get('/bookings/live-statuses', [BookingController::class, 'liveStatuses'])
        ->name('admin.bookings.live_statuses');

    Route::get('/bookings/{id}/invoice/download', [
        BookingController::class,
        'downloadInvoice',
    ])->name('admin.bookings.invoice.download');

    Route::get('/bookings/{id}/invoice', [
        BookingController::class,
        'invoice',
    ])->name('admin.bookings.invoice');
    // Quản lý tài khoản (Users)
    Route::get('/refunds', [RefundController::class, 'index'])->name('admin.refunds.index');
    Route::post('/refunds/{id}/process', [RefundController::class, 'process'])->name('admin.refunds.process');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)
        ->except(['show'])
        ->names('admin.users');

    Route::get('/bookings/identity/{filename}', [BookingController::class, 'viewIdentityImage'])->name('admin.bookings.identity.image');
    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('admin.bookings.update_status');

    Route::post('/bookings/{id}/pnr', [BookingController::class, 'updatePnr'])
        ->name('admin.bookings.update_pnr');

    // Lịch trình Tour
    Route::get('tours/{tour}/itineraries', [TourItineraryController::class, 'index'])
        ->name('admin.tours.itineraries.index');

    Route::post('tours/{tour}/itineraries', [TourItineraryController::class, 'store'])
        ->name('admin.tours.itineraries.store');

    Route::put('itineraries/{itinerary}', [TourItineraryController::class, 'update'])
        ->name('admin.itineraries.update');

    Route::delete('itineraries/{itinerary}', [TourItineraryController::class, 'destroy'])
        ->name('admin.itineraries.destroy');

    // Hoạt động Tour
    Route::post('itineraries/{itinerary}/activities', [TourActivityController::class, 'store'])
        ->name('admin.itineraries.activities.store');

    Route::put('activities/{activity}', [TourActivityController::class, 'update'])
        ->name('admin.activities.update');

    Route::delete('activities/{activity}', [TourActivityController::class, 'destroy'])
        ->name('admin.activities.destroy');

    // Điểm đến
    Route::get('/destinations/trash', [DestinationController::class, 'trash'])->name('admin.destinations.trash');
    Route::post('/destinations/{id}/restore', [DestinationController::class, 'restore'])->name('admin.destinations.restore');
    Route::delete('/destinations/{id}/force-delete', [DestinationController::class, 'forceDelete'])->name('admin.destinations.force-delete');
    Route::resource('destinations', DestinationController::class)
        ->except(['show'])
        ->names('admin.destinations');

    // Lưu trú (Accommodations)
    Route::resource('accommodations', AccommodationController::class)
        ->except(['show'])
        ->names('admin.accommodations');

    // Danh mục
    Route::get('/categories/trash', [CategoryController::class, 'trash'])->name('admin.categories.trash');
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('admin.categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('admin.categories.force-delete');
    Route::resource('categories', CategoryController::class)
        ->except(['show'])
        ->names('admin.categories');

    // Banner
    Route::get('/banners/trash', [BannerController::class, 'trash'])->name('admin.banners.trash');
    Route::post('/banners/{id}/restore', [BannerController::class, 'restore'])->name('admin.banners.restore');
    Route::delete('/banners/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('admin.banners.force-delete');
    Route::resource('banners', BannerController::class)
        ->except(['show'])
        ->names('admin.banners');

    // Tour
    Route::get('/tours', [TourController::class, 'index'])
        ->name('admin.tours.index');

    Route::get('/tours/create', [TourController::class, 'create'])
        ->name('admin.tours.create');

    Route::post('/tours', [TourController::class, 'store'])
        ->name('admin.tours.store');

    Route::get('/tours/{id}/edit', [TourController::class, 'edit'])
        ->name('admin.tours.edit');

    Route::put('/tours/{id}', [TourController::class, 'update'])
        ->name('admin.tours.update');

    Route::delete('/tours/{id}', [TourController::class, 'destroy'])
        ->name('admin.tours.destroy');

    // Thùng rác Tour
    Route::get('/tours/trash', [TourController::class, 'trash'])
        ->name('admin.tours.trash');

    Route::post('/tours/{id}/restore', [TourController::class, 'restore'])
        ->name('admin.tours.restore');

    Route::delete('/tours/{id}/force-delete', [TourController::class, 'forceDelete'])
        ->name('admin.tours.force-delete');

    // Lịch khởi hành Tour
    Route::get('/tours/{id}/schedules', [TourController::class, 'schedules'])
        ->name('admin.tours.schedules');

    Route::post('/tours/{id}/schedules', [TourController::class, 'storeSchedule'])
        ->name('admin.tours.schedules.store');

    // Ảnh Tour
    Route::get('/tours/{id}/images', [TourController::class, 'images'])
        ->name('admin.tours.images');

    Route::post('/tours/{id}/images', [TourController::class, 'storeImages'])
        ->name('admin.tours.images.store');

    Route::post('/tours/{tourId}/images/{imageId}/set-primary', [TourController::class, 'setPrimaryImage'])
        ->name('admin.tours.images.set-primary');

    Route::delete('/tours/{tourId}/images/{imageId}', [TourController::class, 'destroyImage'])
        ->name('admin.tours.images.destroy');

    // Hướng dẫn viên
    Route::resource('tour-guides', TourGuideController::class)
        ->except(['show'])
        ->names('admin.tour_guides');

    // Điều hành Tour
    Route::get('/ongoing-tours', [OngoingTourController::class, 'index'])
        ->name('admin.ongoing_tours.index');

    Route::post('/ongoing-tours/{schedule}/assign-guides', [OngoingTourController::class, 'assignGuides'])
        ->name('admin.ongoing_tours.assign_guides');

    // Holidays
    Route::resource('holidays', HolidayController::class)
        ->names('admin.holidays');

    // Addons
    Route::get('/addons/trash', [AddonController::class, 'trash'])->name('admin.addons.trash');
    Route::post('/addons/{id}/restore', [AddonController::class, 'restore'])->name('admin.addons.restore');
    Route::delete('/addons/{id}/force-delete', [AddonController::class, 'forceDelete'])->name('admin.addons.force-delete');
    Route::resource('addons', AddonController::class)
        ->names('admin.addons');

    // Tickets - Vé tham quan
    Route::get('/tickets/trash', [App\Http\Controllers\Admin\TicketController::class, 'trash'])->name('admin.tickets.trash');
    Route::post('/tickets/{id}/restore', [App\Http\Controllers\Admin\TicketController::class, 'restore'])->name('admin.tickets.restore');
    Route::delete('/tickets/{id}/force-delete', [App\Http\Controllers\Admin\TicketController::class, 'forceDelete'])->name('admin.tickets.force-delete');
    Route::resource('tickets', App\Http\Controllers\Admin\TicketController::class)
        ->except(['show'])
        ->names('admin.tickets');

    Route::delete('tickets/{ticketId}/images/{imageId}', [App\Http\Controllers\Admin\TicketController::class, 'destroyImage'])
        ->name('admin.tickets.images.destroy');

    Route::post('tickets/{ticketId}/images/{imageId}/set-primary', [App\Http\Controllers\Admin\TicketController::class, 'setPrimaryImage'])
        ->name('admin.tickets.images.set-primary');

});

/*
|--------------------------------------------------------------------------
| Guide Interface
|--------------------------------------------------------------------------
*/
Route::prefix('guide')->middleware(['auth', 'guide'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Guide\DashboardController::class, 'index'])
        ->name('guide.dashboard');

    Route::get('/schedules', [ScheduleController::class, 'index'])
        ->name('guide.schedules.index');

    Route::get('/schedules/{id}', [ScheduleController::class, 'show'])
        ->name('guide.schedules.show');

    Route::post('/passengers/{passenger}/toggle-checkin', [ScheduleController::class, 'toggleCheckin'])
        ->name('guide.passengers.toggle_checkin');

    Route::post('/schedules/{schedule}/activities/{activity}/toggle-checkin', [ScheduleController::class, 'toggleActivityCheckin'])
        ->name('guide.activities.toggle_checkin');

    Route::post('/schedules/{schedule}/activities/{activity}/passengers/{passenger}/toggle-checkin', [ScheduleController::class, 'togglePassengerActivityCheckin'])
        ->name('guide.activities.passengers.toggle_checkin');
    Route::post('/schedules/{schedule}/activities/{activity}/checkin-all', [ScheduleController::class, 'checkinAllActivityPassengers'])
        ->name('guide.activities.passengers.checkin_all');
    Route::post('/schedules/{schedule}/activities/{activity}/uncheck-all', [ScheduleController::class, 'uncheckAllActivityPassengers'])
        ->name('guide.activities.passengers.uncheck_all');

    Route::post('/passengers/{passenger}/note', [ScheduleController::class, 'updateNote'])
        ->name('guide.passengers.update_note');

    Route::post('/schedules/{schedule}/checkin-location', [ScheduleController::class, 'updateCheckinLocation'])
        ->name('guide.schedules.update_checkin_location');

    Route::post('/bookings/{booking}/status', [ScheduleController::class, 'updateBookingStatus'])
        ->name('guide.bookings.update_status');

    Route::post('/schedules/{schedule}/save-attendance', [ScheduleController::class, 'saveAttendance'])
        ->name('guide.schedules.save_attendance');

    Route::post('/schedules/{schedule}/update-status', [ScheduleController::class, 'updateGroupStatus'])
        ->name('guide.schedules.update_group_status');

    // Guide passenger actions
    Route::post('/schedules/{schedule}/bookings/{booking}/passengers/manual', [ScheduleController::class, 'storeManualPassengers'])->name('guide.passengers.manual');
    Route::post('/schedules/{schedule}/bookings/{booking}/passengers/import', [ScheduleController::class, 'importExcelPassengers'])->name('guide.passengers.import');
    Route::post('/schedules/{schedule}/passengers/{passenger}/free-time', [ScheduleController::class, 'updateFreeTime'])->name('guide.passengers.free_time');

    // Group Splits
    Route::post('/schedules/{schedule}/passengers/{passenger}/group-split', [GroupSplitController::class, 'store'])->name('guide.passengers.group_split.store');
    Route::post('/group-splits/{groupSplit}/extend', [GroupSplitController::class, 'extend'])->name('guide.group_splits.extend');
    Route::post('/group-splits/{groupSplit}/return', [GroupSplitController::class, 'return'])->name('guide.group_splits.return');
    Route::post('/group-splits/{groupSplit}/cancel', [GroupSplitController::class, 'cancel'])->name('guide.group_splits.cancel');
    Route::get('/group-splits', [GroupSplitController::class, 'index'])->name('guide.group_splits.index');

    // Guide Reports
    Route::get('/schedules/{schedule}/report', [TourReportController::class, 'create'])->name('guide.reports.create');
    Route::post('/schedules/{schedule}/report', [TourReportController::class, 'store'])->name('guide.reports.store');

    // Guide Absence Request
    Route::get('/schedules/{schedule}/absence', [AbsenceRequestController::class, 'create'])
        ->name('guide.schedules.absence');
    Route::post('/schedules/{schedule}/absence', [AbsenceRequestController::class, 'store'])
        ->name('guide.schedules.absence.store');
});

// Thêm Admin TourReport
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/tour-reports', [App\Http\Controllers\Admin\TourReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/tour-reports/{report}', [App\Http\Controllers\Admin\TourReportController::class, 'show'])->name('admin.reports.show');
    Route::post('/tour-reports/{report}/approve', [App\Http\Controllers\Admin\TourReportController::class, 'approve'])->name('admin.reports.approve');
    Route::post('/tour-reports/{report}/reject', [App\Http\Controllers\Admin\TourReportController::class, 'reject'])->name('admin.reports.reject');

    // Admin Absence Requests
    Route::get('/absence-requests/available-guides/{schedule}', [App\Http\Controllers\Admin\AbsenceRequestController::class, 'getAvailableGuides'])
        ->name('admin.absence_requests.available_guides');
    Route::post('/absence-requests/{absenceRequest}/approve', [App\Http\Controllers\Admin\AbsenceRequestController::class, 'approve'])
        ->name('admin.absence_requests.approve');
    Route::post('/absence-requests/{absenceRequest}/reject', [App\Http\Controllers\Admin\AbsenceRequestController::class, 'reject'])
        ->name('admin.absence_requests.reject');

    // Admin Settings
    Route::get('/settings', [SettingController::class, 'index'])
        ->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])
        ->name('admin.settings.update');
});

Route::get('/admin/coupons', [CouponController::class, 'index'])
    ->name('admin.coupons.index');
Route::get('/admin/coupons/create', [CouponController::class, 'create'])
    ->name('admin.coupons.create');
Route::post('/admin/coupons', [CouponController::class, 'store'])
    ->name('admin.coupons.store');
Route::get('/admin/coupons/{coupon}/edit', [CouponController::class, 'edit'])
    ->name('admin.coupons.edit');
Route::put('/admin/coupons/{coupon}', [CouponController::class, 'update'])
    ->name('admin.coupons.update');
Route::delete('/admin/coupons/{coupon}', [CouponController::class, 'destroy'])
    ->name('admin.coupons.destroy');

Route::get('/coupons/trash', [CouponController::class, 'trash'])
    ->name('admin.coupons.trash');

Route::post('/coupons/{id}/restore', [CouponController::class, 'restore'])
    ->name('admin.coupons.restore');

Route::delete('/coupons/{id}/force-delete', [CouponController::class, 'forceDelete'])
    ->name('admin.coupons.forceDelete');
Route::resource('coupons', CouponController::class);
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|*/

Route::get('/api/check-email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
})->name('api.check-email');

require __DIR__.'/auth.php';

Route::get('/report-wrong-email/{code}', function ($code) {
    $id = (int) str_replace('BK-', '', $code);
    $booking = Booking::findOrFail($id);
    $booking->is_reported = true;
    $booking->save();

    return 'Cảm ơn bạn đã báo cáo. Chúng tôi đã ghi nhận và sẽ xử lý.';
});
// CHAT ROUTES
Route::middleware(['auth'])->prefix('chat')->group(function () {
    Route::post('/start', [ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('/{id}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/{id}/messages/{messageId}/mark-important', [ChatController::class, 'markImportant'])->name('chat.mark_important');
    Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread_count');
    Route::post('/{id}/mark-as-read', [ChatController::class, 'markAsRead'])->name('chat.mark_as_read');
});

Route::post('/my-bookings/{booking}/request-invoice', [
    InvoiceRequestController::class,
    'store',
])->name('frontend.bookings.invoice.request');
Route::post('/my-bookings/{booking}/request-invoice', [
    InvoiceRequestController::class,
    'store',
])->name('frontend.bookings.invoice.request');
Route::get('/bookings/{id}/invoice/download', [
    BookingController::class,
    'downloadInvoice',
])->name('admin.bookings.invoice.download');

Route::get('/bookings/{id}/invoice', [
    BookingController::class,
    'invoice',
])->name('admin.bookings.invoice');

Route::post('/bookings/{id}/invoice/send', [
    BookingController::class,
    'sendInvoice',
])->name('admin.bookings.invoice.send');
