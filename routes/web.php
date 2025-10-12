<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Debug route: show current authenticated user relevant fields
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

// Example role-protected route: chỉ 'quan_ly' mới truy cập được
Route::get('/admin-only', function () {
    return 'Trang chỉ dành cho quản lý';
})->middleware(['auth', 'role:quan_ly'])->name('admin.only');

// Admin routes (dashboard, rap management)
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RapController;
use App\Http\Controllers\Admin\SanPhamController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:quan_ly'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('rap', RapController::class)->names('rap');
        Route::resource('san_pham', SanPhamController::class)->names('san_pham');
    });

require __DIR__.'/auth.php';
