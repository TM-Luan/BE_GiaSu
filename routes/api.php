<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiaSuController;
use App\Http\Controllers\NguoiHocController;

 Route::resource('giasu', GiaSuController::class);
Route::resource('nguoihoc', NguoiHocController::class);
 Route::post('/register', [AuthController::class, 'register']);
 Route::post('/login', [AuthController::class, 'login']);
  Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });