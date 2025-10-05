<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

// Movies listing and detail
Route::get('/movies', [BookingController::class, 'movies'])->name('movies.index');
Route::get('/movies/{phim}', [BookingController::class, 'showMovie'])->name('movies.show');

// Seat selection
Route::get('/showtimes/{suatChieu}/seats', [BookingController::class, 'seats'])->name('seats.select');
Route::post('/showtimes/{suatChieu}/hold', [BookingController::class, 'hold'])->name('seats.hold');

// Checkout
Route::get('/checkout/{suatChieu}', [BookingController::class, 'checkout'])->name('checkout.show');
Route::post('/checkout/{suatChieu}', [BookingController::class, 'processCheckout'])->name('checkout.process');

// Payment and success
Route::match(['GET','POST'],'/orders/{donDatVe}/pay', [BookingController::class, 'pay'])->name('orders.pay');
Route::get('/orders/{donDatVe}/success', [BookingController::class, 'success'])->name('orders.success');
