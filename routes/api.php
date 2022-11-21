<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BookingController;

Route::apiResource('users', UserController::class);
Route::apiResource('vehicles', VehicleController::class);

Route::post('/booking', [BookingController::class, 'booking'])->name('booking');
Route::post('/unBooking', [BookingController::class, 'unBooking'])->name('unBooking');
