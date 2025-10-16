<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Controllers dùng chung
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
// Controllers của Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RapController as AdminRapController;
use App\Http\Controllers\Admin\SanPhamController as AdminSanPhamController;
use App\Http\Controllers\Admin\DanhMucController as AdminDanhMucController;
use App\Http\Controllers\Admin\PhimController as AdminPhimController;
use App\Http\Controllers\Admin\ComboController as AdminComboController;
use App\Http\Controllers\Admin\NguoiDungController as AdminNguoiDungController;
use App\Http\Controllers\Admin\DiemTichLuyController as AdminDiemTichLuyController;
use App\Http\Controllers\Admin\PhongChieuController as AdminPhongChieuController;
use App\Http\Controllers\Admin\SuatChieuController as AdminSuatChieuController;

// Controllers của Staff
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\SanPhamController as StaffSanPhamController;
use App\Http\Controllers\Staff\PhimController as StaffPhimController;
use App\Http\Controllers\Staff\DanhMucController as StaffDanhMucController;

/*
|--------------------------------------------------------------------------
| Trang chính
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard người dùng
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Hồ sơ người dùng (profile)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Route test thông tin người dùng
    Route::get('/me', function () {
        $user = Auth::user();
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'ho_ten' => $user->ho_ten ?? null,
            'loai_tai_khoan' => $user->loai_tai_khoan ?? null,
            'vai_tro_id' => $user->vai_tro_id ?? null,
            'vai_tro_name' => optional($user->vaiTro)->ten,
        ]);
    })->name('me');

    // Quản lý tài khoản, điểm thưởng
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/rewards', [AccountController::class, 'rewards'])->name('rewards');
        Route::get('/point-history', [AccountController::class, 'pointHistory'])->name('point-history');
        Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('update-profile');
        Route::put('/change-password', [AccountController::class, 'changePassword'])->name('change-password');
        Route::post('/redeem-combo/{comboId}', [AccountController::class, 'redeemCombo'])->name('redeem-combo');
    });
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

        // Quản lý rạp
        Route::resource('rap', AdminRapController::class)->names('rap');
        // Quản lý phòng chiếu
        Route::resource('phongchieu', AdminPhongChieuController::class)->names('phongchieu');
        // Quản lý suất chiếu
        Route::resource('suatchieu', AdminSuatChieuController::class)->names('suatchieu');
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
        Route::post('combo/{id}/restore', [AdminComboController::class, 'restore'])->name('combo.restore');
        Route::delete('combo/{id}/force-delete', [AdminComboController::class, 'forceDelete'])->name('combo.forceDelete');

        // Quản lý người dùng
        Route::resource('nguoi-dung', AdminNguoiDungController::class)->names('nguoi-dung');
        Route::patch('/nguoi-dung/{id}/toggle', [AdminNguoiDungController::class, 'toggleTrangThai'])->name('nguoi-dung.toggle');


        // Quản lý điểm tích lũy
        Route::get('diem-tich-luy', [AdminDiemTichLuyController::class, 'index'])->name('diem-tich-luy.index');
        Route::get('diem-tich-luy/create', [AdminDiemTichLuyController::class, 'create'])->name('diem-tich-luy.create');
        Route::post('diem-tich-luy', [AdminDiemTichLuyController::class, 'store'])->name('diem-tich-luy.store');
        Route::get('diem-tich-luy/statistics', [AdminDiemTichLuyController::class, 'statistics'])->name('diem-tich-luy.statistics');
        Route::get('diem-tich-luy/{nguoiDungId}', [AdminDiemTichLuyController::class, 'show'])->name('diem-tich-luy.show');
        Route::delete('diem-tich-luy/{id}', [AdminDiemTichLuyController::class, 'destroy'])->name('diem-tich-luy.destroy');
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
    });

/*
|--------------------------------------------------------------------------
| Auth routes (đăng nhập, đăng ký, v.v.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
