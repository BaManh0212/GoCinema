<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RapController;
use App\Http\Controllers\PhongChieuController;
use App\Http\Controllers\GheController;
use App\Http\Controllers\PhimController;
use App\Http\Controllers\Admin\DanhMucController;

// === Trang chính của admin ===
Route::get('/admin', function () {
    return view('admin.index'); // resources/views/admin/index.blade.php
})->name('admin.index');

// === Nhóm route cho admin ===
Route::prefix('admin')->as('admin.')->group(function () {

    // === RẠP ===
    Route::get('/rap', [RapController::class, 'index'])->name('rap.index');
    Route::get('/rap/create', [RapController::class, 'create'])->name('rap.create');
    Route::post('/rap', [RapController::class, 'store'])->name('rap.store');
    Route::get('/rap/{id}/edit', [RapController::class, 'edit'])->name('rap.edit');
    Route::put('/rap/{id}', [RapController::class, 'update'])->name('rap.update');
    Route::delete('/rap/{id}', [RapController::class, 'delete'])->name('rap.delete');

    // === DANH MỤC & PHIM ===
    Route::resource('danhmuc', DanhMucController::class);
    Route::resource('phim', PhimController::class);
});
