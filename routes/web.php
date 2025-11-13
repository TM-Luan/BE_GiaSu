<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
// Import 2 Controller Admin mới
use App\Http\Controllers\Admin\GiaSuController;
use App\Http\Controllers\Admin\NguoiHocController;
use App\Http\Controllers\Admin\LopHocController;
use App\Http\Controllers\Admin\GiaoDichController;
use App\Http\Controllers\Admin\KhieuNaiController;

Route::get('/', fn() => redirect()->route('admin.login'));

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Vô hiệu hóa (hoặc xóa) route '/taikhoan' cũ
        // Route::resource('/taikhoan', TaiKhoanController::class);

        // Quản lý Gia sư và Người học
        Route::resource('/giasu', GiaSuController::class);
        Route::resource('/nguoihoc', NguoiHocController::class);
        
        // Quản lý Khóa học
        Route::resource('/lophoc', LopHocController::class);
        
        // Quản lý Giao dịch
        Route::resource('/giaodich', GiaoDichController::class);
        
        // Quản lý Khiếu nại
        Route::resource('/khieunai', KhieuNaiController::class);
    });
});