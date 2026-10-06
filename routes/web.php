<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])->name('dashboard');
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');

    // Aksi laboran/kalab
    Route::post('/booking/{booking}/approve',  [BookingController::class, 'approve'])->name('booking.approve');
    Route::post('/booking/{booking}/reject',   [BookingController::class, 'reject'])->name('booking.reject');
    Route::post('/booking/{booking}/handover', [BookingController::class, 'handover'])->name('booking.handover');
    Route::post('/booking/{booking}/return',   [BookingController::class, 'returnBack'])->name('booking.return');
});
