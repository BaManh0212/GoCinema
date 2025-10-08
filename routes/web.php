<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RapController;

// === Nhóm route cho admin ===
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        // === Dashboard ===
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // === RẠP ===
        Route::resource('rap', RapController::class);


        // === PHÒNG CHIẾU === (để dùng sau)
        Route::get('/phong', [PhongChieuController::class, 'index'])->name('phong.index');
        Route::get('/phong/create', [PhongChieuController::class, 'create'])->name('phong.create');
        Route::post('/phong', [PhongChieuController::class, 'store'])->name('phong.store');

        // === GHẾ === (để dùng sau)
        Route::get('/ghe', [GheController::class, 'index'])->name('ghe.index');
    });
