<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;

Route::apiResource('users', UserController::class);
Route::apiResource('vehicles', VehicleController::class);
