<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Controllers dùng chung
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PolicyController;
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
// Controllers của Staff
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\SanPhamController as StaffSanPhamController;
use App\Http\Controllers\Staff\PhimController as StaffPhimController;
use App\Http\Controllers\Staff\DanhMucController as StaffDanhMucController;
use App\Http\Controllers\Staff\ComboController as StaffComboController;
use App\Http\Controllers\Staff\DonDatVeController as StaffDonDatVeController;

/*
|--------------------------------------------------------------------------
| Trang chính
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/policies', [PolicyController::class, 'index'])->name('policies');


/*
|--------------------------------------------------------------------------
| Hồ sơ người dùng (profile)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account', [accountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [accountController::class, 'update'])->name('account.update');
    Route::delete('/account', [accountController::class, 'destroy'])->name('account.destroy');


    // Quản lý tài khoản, điểm thưởng
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/rewards', [AccountController::class, 'rewards'])->name('rewards');
        Route::get('/my-vouchers', [AccountController::class, 'myVouchers'])->name('my-vouchers');
        Route::get('/point-history', [AccountController::class, 'pointHistory'])->name('point-history');
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
        // Route::get('phongchieu/{id}/ghe', [GheController::class, 'index'])->name('phongchieu.ghe');
        // Route::post('{id}/ghe', [GheController::class, 'store'])->name('phongchieu.ghe.store');
        // Route::delete('ghe/{id}', [GheController::class, 'destroy'])->name('phongchieu.ghe.destroy');

        Route::get('phongchieu/{id}/ghe', [GheController::class, 'index'])->name('phongchieu.ghe');
        Route::post('phongchieu/{id}/ghe', [GheController::class, 'store'])->name('phongchieu.ghe.store');
        Route::delete('ghe/{id}', [GheController::class, 'destroy'])->name('phongchieu.ghe.destroy');
        Route::post('admin/phongchieu/{id}/ghe/update-map', [GheController::class, 'updateMap'])
        ->name('admin.phongchieu.ghe.updateMap');
        // Quản lý suất chiếu
    Route::resource('suatchieu', AdminSuatChieuController::class)->names('suatchieu');

    // Tạo nhanh tự động
    Route::post('suatchieu/auto-store', [AdminSuatChieuController::class, 'autoStore'])
        ->name('suatchieu.autoStore');

    // Danh sách ghế trong suất chiếu
    Route::get('suatchieu/{id}/ghe', [AdminSuatChieuController::class, 'gheIndex'])
        ->name('suatchieu.ghe');

    // Cập nhật trạng thái từng suất
    Route::patch('suatchieu/{id}/trang-thai', [AdminSuatChieuController::class, 'updateTrangThai'])
        ->name('suatchieu.updateTrangThai');

    // Cập nhật trạng thái hàng loạt
    Route::post('suatchieu/bulk-update', [AdminSuatChieuController::class, 'bulkUpdate'])
        ->name('suatchieu.bulkUpdate');


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
//Quản lý banner
    Route::resource('banners', BannerController::class)->names('banners');
    Route::post('banners/{id}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

//Quản lý liên hệ
    // Quản lý liên hệ
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [AdminContactController::class, 'index'])->name('index');
        Route::get('/{contact}', [AdminContactController::class, 'show'])->name('show');
        Route::post('/{contact}/reply', [AdminContactController::class, 'reply'])->name('reply');
        Route::post('/{contact}/mark-read', [AdminContactController::class, 'markRead'])->name('markRead');
        Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [AdminContactController::class, 'bulkDelete'])->name('bulk-delete');
    });

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
        Route::get('{id}', [StaffDonDatVeController::class, 'show'])->name('show');
        
        // Trang check-in (form)
        Route::get('checkin/form', [StaffDonDatVeController::class, 'showCheckinForm'])->name('checkin');
        
        // Xử lý check-in bằng mã đơn
        Route::post('checkin/code', [StaffDonDatVeController::class, 'checkInByCode'])->name('checkinByCode');
        
        // Thay đổi trạng thái đơn
        Route::post('{id}/change-status', [StaffDonDatVeController::class, 'changeStatus'])->name('changeStatus');
        
        // In vé (PDF)
        Route::get('{id}/print', [StaffDonDatVeController::class, 'print'])->name('print');
    });
    });
    
/*
|--------------------------------------------------------------------------
| Auth routes (đăng nhập, đăng ký, v.v.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
