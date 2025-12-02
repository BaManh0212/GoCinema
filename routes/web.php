<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Controllers dùng chung
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PhimController;
use App\Http\Controllers\BaiVietController;
use App\Http\Controllers\ChatbotController;
// Controllers của Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\RapController as AdminRapController;
use App\Http\Controllers\Admin\SanPhamController as AdminSanPhamController;
use App\Http\Controllers\Admin\DanhMucController as AdminDanhMucController;
use App\Http\Controllers\Admin\PhimController as AdminPhimController;
use App\Http\Controllers\Admin\ComboController as AdminComboController;
use App\Http\Controllers\Admin\NguoiDungController as AdminNguoiDungController;
use App\Http\Controllers\Admin\DiemTichLuyController as AdminDiemTichLuyController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\MaGiamGiaController;
use App\Http\Controllers\Admin\PhongChieuController as AdminPhongChieuController;
use App\Http\Controllers\Admin\SuatChieuController as AdminSuatChieuController;
use App\Http\Controllers\Admin\DonDatVeController as AdminDonDatVeController;
use App\Http\Controllers\Admin\GheController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\BaiVietController as AdminBaiVietController;
use App\Http\Controllers\Admin\LogController;
// Controllers của Staff
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\SanPhamController as StaffSanPhamController;
use App\Http\Controllers\Staff\PhimController as StaffPhimController;
use App\Http\Controllers\Staff\DanhMucController as StaffDanhMucController;
use App\Http\Controllers\Staff\ComboController as StaffComboController;
use App\Http\Controllers\Staff\DonDatVeController as StaffDonDatVeController;
use App\Http\Controllers\Staff\SuatChieuController as StaffSuatChieuController;
use App\Http\Controllers\Staff\PhongChieuController as StaffPhongChieuController;
use App\Http\Controllers\Staff\GheController as StaffGheController;
use App\Http\Controllers\Admin\SoDoGheController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| Trang chính
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/policies', [PolicyController::class, 'index'])->name('policies');

// Danh sách phim theo danh mục (public)
Route::get('/phim', [PhimController::class, 'index'])->name('movies.index');
Route::get('/danh-muc/{slug}', [PhimController::class, 'category'])->name('movies.category');

// Trang chi tiết phim
Route::get('/phim/{slug}', [PhimController::class, 'show'])->name('movies.show');

// Trang lịch chiếu
Route::get('/lich-chieu', [PhimController::class, 'schedule'])->name('schedule.index');

// Đặt vé
Route::get('/booking', [App\Http\Controllers\BookingController::class, 'index'])->name('booking.index');
Route::post('/booking/hold-seats', [App\Http\Controllers\BookingController::class, 'holdSeats'])->name('booking.holdSeats');
Route::post('/booking/release-seats', [App\Http\Controllers\BookingController::class, 'releaseSeats'])->name('booking.releaseSeats');
Route::post('/booking/check-voucher', [App\Http\Controllers\BookingController::class, 'checkVoucher'])->name('booking.check-voucher');
Route::post('/booking/ajax-cancel/{id}', [App\Http\Controllers\BookingController::class, 'ajaxCancel'])->whereNumber('id')->name('booking.ajax-cancel');
Route::get('/booking/payment/{id}', [App\Http\Controllers\BookingController::class, 'payment'])->whereNumber('id')->middleware('auth')->name('booking.payment');
Route::post('/booking/process-payment/{id}', [App\Http\Controllers\BookingController::class, 'processPayment'])->whereNumber('id')->middleware('auth')->name('booking.process-payment');
Route::get('/booking/confirm/{id}', [App\Http\Controllers\BookingController::class, 'confirm'])->whereNumber('id')->middleware('auth')->name('booking.confirm');
Route::delete('/booking/{id}', [App\Http\Controllers\BookingController::class, 'cancel'])->whereNumber('id')->name('booking.cancel');
Route::post('/booking/momo-callback', [App\Http\Controllers\BookingController::class, 'momoCallback'])->name('booking.momo-callback');
Route::get('/booking/momo-return', [App\Http\Controllers\BookingController::class, 'momoReturn'])->name('booking.momo-return');
Route::get('/booking/vnpay-return', [App\Http\Controllers\BookingController::class, 'vnpayReturn'])->name('booking.vnpay-return');
Route::post('/booking/vnpay-callback', [App\Http\Controllers\BookingController::class, 'vnpayCallback'])->name('booking.vnpay-callback');
Route::post('/booking', [App\Http\Controllers\BookingController::class, 'store'])->name('booking.store');

// JSON lịch chiếu (nếu cần load bằng JS) + Lưu đánh giá
Route::get('/api/phim/{slug}/lich-chieu', [PhimController::class, 'lichChieuJson'])->name('movies.schedule.json');
Route::post('/phim/{slug}/danh-gia', [PhimController::class, 'luuDanhGia'])
    ->middleware('auth')
    ->name('phim.danh_gia.luu');
// (removed duplicate legacy booking routes that referenced non-existent methods)

// Đơn vé
Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
//Bài viết
Route::get('/tin-tuc', [BaiVietController::class, 'index'])->name('baiviet.index');
Route::get('/tin-tuc/{slug}', [BaiVietController::class, 'show'])->name('baiviet.show');
// Chatbot
Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index'); // trang hiển thị
Route::get('/chatbot/test', [ChatbotController::class, 'test'])->name('chatbot.test'); // test endpoint
Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.send'); // gửi message
Route::post('/chatbot/clear', [ChatbotController::class, 'clearChat'])->name('chatbot.clear');




/*
|--------------------------------------------------------------------------
| Hồ sơ người dùng (profile)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account', [accountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [accountController::class, 'update'])->name('account.update');
    Route::delete('/account', [accountController::class, 'destroy'])->name('account.destroy');
    Route::post('/account/update-avatar', [AccountController::class, 'updateAvatar'])->name('account.update-avatar');


    // Quản lý tài khoản, điểm thưởng
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/rewards', [AccountController::class, 'rewards'])->name('rewards');
        Route::get('/my-vouchers', [AccountController::class, 'myVouchers'])->name('my-vouchers');
        Route::get('/point-history', [AccountController::class, 'pointHistory'])->name('point-history');
        Route::get('/bookings', [AccountController::class, 'bookings'])->name('bookings');
        Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('update-profile');
        Route::put('/change-password', [AccountController::class, 'changePassword'])->name('change-password');
        Route::post('/redeem-voucher/{voucherId}', [AccountController::class, 'redeemVoucher'])->name('redeem-voucher');
    });
    // Liên hệ
    Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/contact/history', [ContactController::class, 'history'])->name('contact.history');
});

/*
|--------------------------------------------------------------------------
| Route chỉ dành cho quản lý
|--------------------------------------------------------------------------
*/
Route::get('/admin-only', function () {
    return 'Trang chỉ dành cho quản lý';
})->middleware(['auth', 'role:quan_ly'])->name('admin.only');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (role: quan_ly)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:quan_ly'])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Reports (drill-down cho dashboard)
        Route::prefix('reports')->name('reports.')->group(function () {
            // Revenue
            Route::get('/revenue/total',    [AdminReportController::class, 'revenueTotal'])->name('revenue.total');
            Route::get('/revenue/tickets',  [AdminReportController::class, 'revenueTickets'])->name('revenue.tickets');
            Route::get('/revenue/combos',   [AdminReportController::class, 'revenueCombos'])->name('revenue.combos');
            Route::get('/revenue/products', [AdminReportController::class, 'revenueProducts'])->name('revenue.products');

            // Tickets & Orders & Payments
            Route::get('/tickets',          [AdminReportController::class, 'tickets'])->name('tickets');
            Route::get('/orders',           [AdminReportController::class, 'orders'])->name('orders');
            Route::get('/orders/canceled',  [AdminReportController::class, 'ordersCanceled'])->name('orders.canceled');
            Route::get('/payments',         [AdminReportController::class, 'payments'])->name('payments');
            Route::get('/refunds',          [AdminReportController::class, 'refunds'])->name('refunds');

            // Movies / Customers / Cinemas
            Route::get('/movies',           [AdminReportController::class, 'movies'])->name('movies');
            Route::get('/movie/{id}',       [AdminReportController::class, 'movieDetail'])->whereNumber('id')->name('movie.detail');

            Route::get('/customers',        [AdminReportController::class, 'customers'])->name('customers');
            Route::get('/customer/{id}',    [AdminReportController::class, 'customerDetail'])->whereNumber('id')->name('customer.detail');

            Route::get('/cinemas',          [AdminReportController::class, 'cinemas'])->name('cinemas');

            // Order detail
            Route::get('/order/{id}',       [AdminReportController::class, 'orderDetail'])->whereNumber('id')->name('order.detail');
        });

        // Quản lý rạp
        Route::get('/rap', [AdminRapController::class, 'index'])->name('rap.index');
        // Quản lý phòng chiếu
        Route::resource('phongchieu', AdminPhongChieuController::class)->names('phongchieu');
        // Quản lý ghế theo từng phòng
        Route::resource('sodo', SoDoGheController::class)->names('sodo');
        Route::get('/admin/sodoghe/{phong_id}', [SoDoGheController::class, 'show'])
        ->name('admin.sodoghe.show');
        // Xem sơ đồ ghế của suất chiếu
    //   Route::prefix('suatchieu')->name('suatchieu.')->group(function () {
    //     
    // });
        Route::post('/admin/sodo/update-seat-status', [SoDoGheController::class, 'updateSeatStatus'])
        ->name('admin.sodo.updateSeatStatus');
        //  Route::get('/{suatChieu}', [AdminSuatChieuController::class, 'show'])->name('show'); // chi tiết
        // Route::get('phongchieu/{id}/ghe', [GheController::class, 'index'])->name('phongchieu.ghe');
        // Route::post('{id}/ghe', [GheController::class, 'store'])->name('phongchieu.ghe.store');
        // Route::delete('ghe/{id}', [GheController::class, 'destroy'])->name('phongchieu.ghe.destroy');

        Route::get('phongchieu/{id}/ghe', [GheController::class, 'index'])->name('phongchieu.ghe');
        Route::post('phongchieu/{id}/ghe', [GheController::class, 'store'])->name('phongchieu.ghe.store');
        Route::delete('ghe/{id}', [GheController::class, 'destroy'])->name('phongchieu.ghe.destroy');
Route::post('phongchieu/{id}/ghe/update-map', [GheController::class, 'updateMap'])
    ->name('phongchieu.ghe.updateMap');
        Route::post('phongchieu/{id}/ghe/convert-vip', [GheController::class, 'convertRowsToVip'])
            ->name('phongchieu.ghe.convertRowsToVip');
        Route::post('phongchieu/{id}/ghe/convert-normal', [GheController::class, 'convertRowsToNormal'])
            ->name('phongchieu.ghe.convertRowsToNormal');
        Route::post('phongchieu/{id}/ghe/convert-double', [GheController::class, 'convertToDoubleSeats'])
            ->name('phongchieu.ghe.convertToDoubleSeats');

        // 🎬 Suất Chiếu CRUD
    Route::get('/suatchieu', [AdminSuatChieuController::class, 'index'])
        ->name('suatchieu.index');

    Route::get('/suatchieu/create', [AdminSuatChieuController::class, 'create'])
        ->name('suatchieu.create');

    Route::post('/suatchieu', [AdminSuatChieuController::class, 'store'])
        ->name('suatchieu.store');

    Route::get('/suatchieu/{id}/edit', [AdminSuatChieuController::class, 'edit'])
        ->name('suatchieu.edit');

    Route::put('/suatchieu/{id}', [AdminSuatChieuController::class, 'update'])
        ->name('suatchieu.update');

    Route::delete('/suatchieu/{id}', [AdminSuatChieuController::class, 'destroy'])
        ->name('suatchieu.destroy');

    // 🎟️ Xem sơ đồ ghế
    Route::get('/suatchieu/{id}/show', [AdminSuatChieuController::class, 'gheIndex'])
        ->name('suatchieu.show');

    // 🔄 API cập nhật trạng thái ghế
    Route::post('/suatchieu/{id}/ghe/update', [AdminSuatChieuController::class, 'updateGheTrangThai'])
        ->name('suatchieu.ghe.update');

    // 🔄 API cập nhật trạng thái giữ tạm ghế (thêm/xóa giữ tạm)
    Route::post('/suatchieu/{id}/ghe/update-giu-tam', [AdminSuatChieuController::class, 'updateGheGiuTam'])
        ->name('suatchieu.ghe.updateGiuTam');

    // 🟢 Lấy trạng thái ghế real-time
    Route::get('/suatchieu/{id}/seat-status', [AdminSuatChieuController::class, 'seatStatus'])
        ->name('suatchieu.seatStatus');

    // ⚙️ Auto tạo suất chiếu
    Route::post('/suatchieu/auto-store', [AdminSuatChieuController::class, 'autoStore'])
        ->name('suatchieu.autoStore');

    // 💾 Lưu preview
    Route::post('/suatchieu/store-preview', [AdminSuatChieuController::class, 'storePreview'])
        ->name('suatchieu.storePreview');

    // 🔄 Cập nhật trạng thái suất chiếu
    Route::post('/suatchieu/{id}/update-status', [AdminSuatChieuController::class, 'updateTrangThai'])
        ->name('suatchieu.updateTrangThai');

    // 🧩 Update trạng thái hàng loạt
    Route::post('/suatchieu/bulk-update', [AdminSuatChieuController::class, 'bulkUpdate'])
        ->name('suatchieu.bulkUpdate');

    // 🔄 Cập nhật trạng thái ghế (cho route admin.suatchieu.ghe.updateTrangThai)
    Route::patch('/suatchieu/{id}/ghe/update-trang-thai', [AdminSuatChieuController::class, 'updateGheTrangThai'])
        ->name('suatchieu.ghe.updateTrangThai');

    // =========================
    // RESOURCE SUẤT CHIẾU
    // =========================
    
    // scan QR
    Route::get('/admin/scan-qr', [\App\Http\Controllers\Admin\QRController::class, 'scanPage'])
    ->name('admin.scan.qr');

    Route::post('/admin/scan-qr/check', [\App\Http\Controllers\Admin\QRController::class, 'check'])
    ->name('admin.scan.qr.check');

    Route::get('/admin/orders/{ma_don}', [AdminDonDatVeController::class, 'showQR'])
    ->name('admin.orders.showQR');

        // Quản lý danh mục
        Route::get('danhmuc/thung-rac', [AdminDanhMucController::class, 'trashed'])->name('danhmuc.trashed');
        Route::put('danhmuc/{id}/khoi-phuc', [AdminDanhMucController::class, 'restore'])->name('danhmuc.restore');
        Route::delete('danhmuc/{id}/xoa-vinh-vien', [AdminDanhMucController::class, 'forceDelete'])->name('danhmuc.forceDelete');
        Route::resource('danhmuc', AdminDanhMucController::class)->names('danhmuc');


        // Quản lý phim
        Route::get('phim/thung-rac', [AdminPhimController::class, 'trashed'])->name('phim.trashed');
        Route::put('phim/{id}/khoi-phuc', [AdminPhimController::class, 'restore'])->name('phim.restore');
        Route::delete('phim/{id}/xoa-vinh-vien', [AdminPhimController::class, 'forceDelete'])->name('phim.forceDelete');
        Route::resource('phim', AdminPhimController::class)->names('phim');

        // Quản lý sản phẩm
        Route::resource('san_pham', AdminSanPhamController::class)->except(['show']);
        Route::get('san_pham/thung-rac', [AdminSanPhamController::class, 'trashed'])->name('san_pham.trashed');
        Route::put('san_pham/{id}/khoi-phuc', [AdminSanPhamController::class, 'restore'])->name('san_pham.restore');
        Route::delete('san_pham/{id}/xoa-vinh-vien', [AdminSanPhamController::class, 'forceDelete'])->name('san_pham.forceDelete');

        // Quản lý combo
        Route::resource('combo', AdminComboController::class)->except(['show'])->names('combo');
        Route::get('combo/thung-rac', [AdminComboController::class, 'trashed'])->name('combo.trashed');
        Route::put('combo/{id}/restore', [AdminComboController::class, 'restore'])->name('combo.restore');
        Route::delete('combo/{id}/force-delete', [AdminComboController::class, 'forceDelete'])->name('combo.forceDelete');

        // Quản lý người dùng
        Route::patch('/nguoi-dung/{id}/toggle', [AdminNguoiDungController::class, 'toggleTrangThai'])->name('nguoi-dung.toggle');
        Route::resource('nguoi-dung', AdminNguoiDungController::class)->names('nguoi-dung');


        // Quản lý điểm tích lũy
        Route::get('diem-tich-luy', [AdminDiemTichLuyController::class, 'index'])->name('diem-tich-luy.index');
        Route::get('diem-tich-luy/create', [AdminDiemTichLuyController::class, 'create'])->name('diem-tich-luy.create');
        Route::post('diem-tich-luy', [AdminDiemTichLuyController::class, 'store'])->name('diem-tich-luy.store');
        Route::get('diem-tich-luy/statistics', [AdminDiemTichLuyController::class, 'statistics'])->name('diem-tich-luy.statistics');
        Route::get('diem-tich-luy/{nguoiDungId}', [AdminDiemTichLuyController::class, 'show'])->name('diem-tich-luy.show');
        Route::delete('diem-tich-luy/{id}', [AdminDiemTichLuyController::class, 'destroy'])->name('diem-tich-luy.destroy');

        // Quản lý voucher
        // 🧺 Thùng rác
        Route::get('/trashed', [VoucherController::class, 'trashed'])->name('voucher.trashed');
        Route::delete('/{id}', [VoucherController::class, 'destroy'])->name('voucher.destroy');
        Route::put('/{id}/restore', [VoucherController::class, 'restore'])->name('voucher.restore');
        Route::delete('/{id}/force', [VoucherController::class, 'forceDelete'])->name('voucher.forceDelete');
        Route::resource('voucher', VoucherController::class)->names('voucher');
        Route::post('voucher/{id}/toggle-status', [VoucherController::class, 'toggleStatus'])->name('voucher.toggle-status');
        Route::get('voucher-statistics', [VoucherController::class, 'statistics'])->name('voucher.statistics');

        // Quản lý đơn vé
        Route::prefix('donve')->name('donve.')->group(function () {
            // Các route CRUD cơ bản
            Route::get('/', [AdminDonDatVeController::class, 'index'])->name('index');
            Route::get('{id}', [AdminDonDatVeController::class, 'show'])->name('show');

            // Trang check-in (form)
            Route::get('checkin/form', [AdminDonDatVeController::class, 'showCheckinForm'])->name('checkin');

            // Xử lý check-in bằng mã đơn
            Route::post('checkin/code', [AdminDonDatVeController::class, 'checkInByCode'])->name('checkinByCode');

            // Thay đổi trạng thái đơn
            Route::post('{id}/change-status', [AdminDonDatVeController::class, 'changeStatus'])->name('changeStatus');

            // In vé (PDF)
            Route::get('{id}/print', [AdminDonDatVeController::class, 'print'])->name('print');
        });

        // Quản lý mã giảm giá
        Route::prefix('ma_giam_gia')->name('ma_giam_gia.')->group(function () {
            // Thùng rác
            Route::get('trash', [MaGiamGiaController::class, 'trash'])->name('trash');

            // Khôi phục & xóa vĩnh viễn
            Route::put('{id}/restore', [MaGiamGiaController::class, 'restore'])->name('restore');
            Route::delete('{id}/force', [MaGiamGiaController::class, 'forceDelete'])->name('forceDelete');

            // Bật/tắt kích hoạt
            Route::post('{id}/toggle', [MaGiamGiaController::class, 'toggle'])->name('toggle');
        });
        // Quản lý banner
        Route::resource('banners', BannerController::class)->names('banners');
        Route::post('banners/{id}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

        // Add route for admin.logs.index to fix missing route error
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');

        // Quản lý liên hệ
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [AdminContactController::class, 'index'])->name('index');
            Route::get('/{contact}', [AdminContactController::class, 'show'])->name('show');
            Route::post('/{contact}/reply', [AdminContactController::class, 'reply'])->name('reply');
            Route::post('/{contact}/mark-read', [AdminContactController::class, 'markRead'])->name('markRead');
            Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [AdminContactController::class, 'bulkDelete'])->name('bulk-delete');
        });
        // Quan lý bài viết
        Route::resource('baiviet', AdminBaiVietController::class);
        Route::patch('baiviet/{baiviet}/toggle', [AdminBaiVietController::class, 'toggleActive'])->name('baiviet.toggle');

        // Resource CRUD
        Route::resource('ma_giam_gia', MaGiamGiaController::class)
            ->names('ma_giam_gia')
            ->parameters(['ma_giam_gia' => 'maGiamGia']);
    });

/*
|--------------------------------------------------------------------------
| STAFF ROUTES (role: nhan_vien)
|--------------------------------------------------------------------------
*/
Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'role:nhan_vien'])
    ->group(function () {
        // Dashboard
        Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');

        // Quản lý danh mục
        Route::resource('danhmuc', StaffDanhMucController::class)->names('danhmuc');

        // Quản lý phim
        Route::resource('phim', StaffPhimController::class)->names('phim');

        // Quản lý sản phẩm
        Route::resource('san_pham', StaffSanPhamController::class)->except(['show']);

        // Quản lý combo
        Route::resource('combo', StaffComboController::class)->names('combo');
        // Check-in vé (nhân viên)
        Route::prefix('donve')->name('donve.')->group(function () {
            // Các route CRUD cơ bản
            Route::get('/', [StaffDonDatVeController::class, 'index'])->name('index');
            // Tạo đơn vé tại quầy
            Route::get('create', [StaffDonDatVeController::class, 'create'])->name('create');
            Route::get('select-suat/{phim_id}', [StaffDonDatVeController::class, 'selectSuat'])->name('selectSuat');
            Route::get('select-seats/{suat_chieu_id}', [StaffDonDatVeController::class, 'selectSeats'])->name('selectSeats');
            Route::get('{id}', [StaffDonDatVeController::class, 'show'])->name('show');
            Route::post('store', [StaffDonDatVeController::class, 'store'])->name('store');
            Route::get('confirm/{id}', [StaffDonDatVeController::class, 'confirm'])->name('confirm');

            // Trang check-in (form)
            Route::get('checkin/form', [StaffDonDatVeController::class, 'showCheckinForm'])->name('checkin');

            // Xử lý check-in bằng mã đơn
            Route::post('checkin/code', [StaffDonDatVeController::class, 'checkInByCode'])->name('checkinByCode');

            // Thay đổi trạng thái đơn
            Route::post('{id}/change-status', [StaffDonDatVeController::class, 'changeStatus'])->name('changeStatus');

            // In vé (PDF)
            Route::get('{id}/print', [StaffDonDatVeController::class, 'print'])->name('print');
        });
        // Quản lý phòng chiếu
        Route::resource('phongchieu', StaffPhongChieuController::class)->names('phongchieu');
        // Quản lý ghế theo từng phòng
        Route::resource('sodo', SoDoGheController::class)->names('sodo');
        Route::get('/staff/sodoghe/{phong_id}', [SoDoGheController::class, 'show'])
        ->name('staff.sodoghe.show');
        // Xem sơ đồ ghế của suất chiếu
    //   Route::prefix('suatchieu')->name('suatchieu.')->group(function () {
    //     
    // });
        Route::post('/staff/sodo/update-seat-status', [SoDoGheController::class, 'updateSeatStatus'])
        ->name('staff.sodo.updateSeatStatus');
        //  Route::get('/{suatChieu}', [AdminSuatChieuController::class, 'show'])->name('show'); // chi tiết
        // Route::get('phongchieu/{id}/ghe', [GheController::class, 'index'])->name('phongchieu.ghe');
        // Route::post('{id}/ghe', [GheController::class, 'store'])->name('phongchieu.ghe.store');
        // Route::delete('ghe/{id}', [GheController::class, 'destroy'])->name('phongchieu.ghe.destroy');

        Route::get('phongchieu/{id}/ghe', [StaffGheController::class, 'index'])->name('phongchieu.ghe');
        Route::post('phongchieu/{id}/ghe', [StaffGheController::class, 'store'])->name('phongchieu.ghe.store');
        Route::delete('ghe/{id}', [StaffGheController::class, 'destroy'])->name('phongchieu.ghe.destroy');
Route::post('phongchieu/{id}/ghe/update-map', [StaffGheController::class, 'updateMap'])
    ->name('phongchieu.ghe.updateMap');
        Route::post('phongchieu/{id}/ghe/convert-vip', [StaffGheController::class, 'convertRowsToVip'])
            ->name('phongchieu.ghe.convertRowsToVip');
        Route::post('phongchieu/{id}/ghe/convert-normal', [StaffGheController::class, 'convertRowsToNormal'])
            ->name('phongchieu.ghe.convertRowsToNormal');
        Route::post('phongchieu/{id}/ghe/convert-double', [StaffGheController::class, 'convertToDoubleSeats'])
            ->name('phongchieu.ghe.convertToDoubleSeats');
        // Quản lý suất chiếu
        Route::resource('suatchieu', StaffSuatChieuController::class)->names('suatchieu');

        // Tạo nhanh tự động
        Route::post('suatchieu/auto-store', [StaffSuatChieuController::class, 'autoStore'])
            ->name('suatchieu.autoStore');

        // Danh sách ghế trong suất chiếu
        Route::get('suatchieu/{id}/ghe', [StaffSuatChieuController::class, 'gheIndex'])
            ->name('suatchieu.ghe');

        // Cập nhật trạng thái từng suất
        Route::patch('suatchieu/{id}/trang-thai', [StaffSuatChieuController::class, 'updateTrangThai'])
            ->name('suatchieu.updateTrangThai');

        // Cập nhật trạng thái hàng loạt
        Route::post('suatchieu/bulk-update', [StaffSuatChieuController::class, 'bulkUpdate'])
            ->name('suatchieu.bulkUpdate');

        // 🔄 API cập nhật trạng thái ghế
        Route::patch('suatchieu/{id}/ghe/update-trang-thai', [StaffSuatChieuController::class, 'updateGheTrangThai'])
            ->name('suatchieu.ghe.updateTrangThai');

        // 🟢 Lấy trạng thái ghế real-time
        Route::get('suatchieu/{id}/seat-status', [StaffSuatChieuController::class, 'seatStatus'])
            ->name('suatchieu.seatStatus');
    });

/*
|--------------------------------------------------------------------------
| Auth routes (đăng nhập, đăng ký, v.v.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
