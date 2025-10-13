<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RapController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\PhimController;

// Trang welcome
Route::get('/', function () {
    return view('welcome');
});

// Dashboard người dùng
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Hồ sơ người dùng
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
});

// Route chỉ dành cho quản lý
Route::get('/admin-only', function () {
    return 'Trang chỉ dành cho quản lý';
})->middleware(['auth', 'role:quan_ly'])->name('admin.only');

// Admin routes (role: quan_ly)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:quan_ly'])
    ->group(function () {
        // Admin dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Quản lý rạp
        Route::resource('rap', RapController::class)->names('rap');

        // Quản lý danh mục & phim
        Route::resource('danhmuc', DanhMucController::class)->names('danhmuc');
        // Thùng rác / restore / force delete cho phim (đặt trước resource để không bị trùng route 'show')
        Route::get('phim/thung-rac', [PhimController::class, 'trashed'])->name('phim.trashed');
        Route::put('phim/{id}/khoi-phuc', [PhimController::class, 'restore'])->name('phim.restore');
        Route::delete('phim/{id}/xoa-vinh-vien', [PhimController::class, 'forceDelete'])->name('phim.forceDelete');
        Route::resource('phim', PhimController::class)->names('phim');

        // Quản lý sản phẩm
        Route::resource('san_pham', SanPhamController::class)->except(['show']);
        Route::get('san_pham/thung-rac', [SanPhamController::class, 'trashed'])->name('san_pham.trashed');
        Route::put('san_pham/{id}/khoi-phuc', [SanPhamController::class, 'restore'])->name('san_pham.restore');
        Route::delete('san_pham/{id}/xoa-vinh-vien', [SanPhamController::class, 'forceDelete'])->name('san_pham.forceDelete');
    });

require __DIR__ . '/auth.php';
