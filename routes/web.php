<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RapController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\PhimController;

// Trang chính
Route::get('/', function () {
    return view('welcome');
});

// Simple dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔹 Các route liên quan đến người dùng
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Debug route: kiểm tra thông tin user đăng nhập
    Route::get('/me', function () {
        $user = auth()->user();
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

// 🔐 Chỉ quản lý mới được truy cập
Route::get('/admin-only', function () {
    return 'Trang chỉ dành cho quản lý';
})->middleware(['auth', 'role:quan_ly'])->name('admin.only');

// =========================
// ⚙️ ADMIN ROUTES
// =========================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:quan_ly'])
    ->group(function () {

        // Admin dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Quản lý rạp (resource)
        Route::resource('rap', RapController::class)->names('rap');

        // Quản lý danh mục & phim (nếu có)
        Route::resource('danhmuc', DanhMucController::class)->names('danhmuc');
        Route::resource('phim', PhimController::class)->names('phim');

        // Quản lý sản phẩm (ví dụ khác)
        Route::resource('san_pham', SanPhamController::class)->except(['show']);

        // 🗑️ Các route riêng cho Thùng rác (san_pham)
        Route::get('san_pham/thung-rac', [SanPhamController::class, 'trashed'])
            ->name('san_pham.trashed');

        Route::put('san_pham/{id}/khoi-phuc', [SanPhamController::class, 'restore'])
            ->name('san_pham.restore');

        Route::delete('san_pham/{id}/xoa-vinh-vien', [SanPhamController::class, 'forceDelete'])
            ->name('san_pham.forceDelete');
    });

require __DIR__.'/auth.php';
