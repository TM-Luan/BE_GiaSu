<?php

use Illuminate\Support\Facades\Route;

// 1. IMPORT CONTROLLERS
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GiaSuController;
use App\Http\Controllers\Admin\NguoiHocController;
use App\Http\Controllers\Admin\LopHocController as AdminLopHocController; // Đổi tên để tránh trùng
use App\Http\Controllers\Admin\GiaoDichController;
use App\Http\Controllers\Admin\KhieuNaiController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\GiaSuDashboardController;
use App\Http\Controllers\Web\NguoiHocDashboardController;
use App\Http\Controllers\Web\LopHocController; // <-- THÊM CONTROLLER LỚP HỌC MỚI

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect mặc định
Route::get('/', fn() => redirect()->route('login')); 

// Đăng nhập / Đăng xuất (Web)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// ===== KHU VỰC NGƯỜI DÙNG (Cần sửa ở đây) =====
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/giasu/dashboard', [GiaSuDashboardController::class, 'index'])->name('giasu.dashboard');
    Route::get('/nguoihoc/dashboard', [NguoiHocDashboardController::class, 'index'])->name('nguoihoc.dashboard');

    // Xem hồ sơ gia sư
    Route::get('/giasu/ho-so/{id}', [NguoiHocDashboardController::class, 'show'])->name('nguoihoc.giasu.show');

    // Mời dạy
    Route::post('/nguoihoc/moi-day', [NguoiHocDashboardController::class, 'moiDay'])->name('nguoihoc.moi_day');

    // === [MỚI] QUẢN LÝ LỚP HỌC CỦA TÔI ===
    Route::get('/nguoihoc/lop-hoc', [LopHocController::class, 'index'])->name('nguoihoc.lophoc.index');
    Route::get('/nguoihoc/lop-hoc/tao-moi', [LopHocController::class, 'create'])->name('nguoihoc.lophoc.create');
    Route::post('/nguoihoc/lop-hoc', [LopHocController::class, 'store'])->name('nguoihoc.lophoc.store');
    // 1. Trang danh sách đề nghị của một lớp
    Route::get('/nguoihoc/lop-hoc/{id}/de-nghi', [LopHocController::class, 'showProposals'])->name('nguoihoc.lophoc.proposals');

    // 2. Hành động Chấp nhận gia sư
    Route::post('/nguoihoc/de-nghi/{yeuCauId}/chap-nhan', [LopHocController::class, 'acceptProposal'])->name('nguoihoc.proposals.accept');

    // 3. Hành động Từ chối gia sư
    Route::post('/nguoihoc/de-nghi/{yeuCauId}/tu-choi', [LopHocController::class, 'rejectProposal'])->name('nguoihoc.proposals.reject');
    Route::get('/nguoihoc/lop-hoc/{id}/sua', [LopHocController::class, 'edit'])->name('nguoihoc.lophoc.edit');
    Route::put('/nguoihoc/lop-hoc/{id}', [LopHocController::class, 'update'])->name('nguoihoc.lophoc.update');
    Route::post('/nguoihoc/lop-hoc/{id}/huy', [LopHocController::class, 'cancel'])->name('nguoihoc.lophoc.cancel');
    Route::delete('/nguoihoc/lop-hoc/{id}', [LopHocController::class, 'destroy'])->name('nguoihoc.lophoc.destroy');
    Route::get('/nguoihoc/lop-hoc/{id}', [LopHocController::class, 'show'])->name('nguoihoc.lophoc.show');
    // === [MỚI] KHIẾU NẠI LỚP HỌC ===
    // 1. Hiển thị form khiếu nại
    Route::get('/nguoihoc/lop-hoc/{id}/khieu-nai', [LopHocController::class, 'createComplaint'])
        ->name('nguoihoc.lophoc.complaint.create');
    // 2. Gửi khiếu nại
    Route::post('/nguoihoc/lop-hoc/{id}/khieu-nai', [LopHocController::class, 'storeComplaint'])
        ->name('nguoihoc.lophoc.complaint.store');
        // === [MỚI] LỊCH HỌC CỦA TÔI ===
    Route::get('/nguoihoc/lich-hoc', [App\Http\Controllers\Web\LichHocWebController::class, 'index'])
        ->name('nguoihoc.lichhoc.index');
        // === [MỚI] THÔNG TIN CÁ NHÂN ===
        // === [CẬP NHẬT] THÔNG TIN CÁ NHÂN ===
    Route::get('/nguoihoc/thong-tin-ca-nhan', [App\Http\Controllers\Web\ProfileController::class, 'index'])
        ->name('nguoihoc.profile.index');
    Route::put('/nguoihoc/thong-tin-ca-nhan', [App\Http\Controllers\Web\ProfileController::class, 'update'])
        ->name('nguoihoc.profile.update');
    // THÊM ROUTE NÀY:
    Route::put('/nguoihoc/doi-mat-khau', [App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])
        ->name('nguoihoc.profile.password.update');
        // === [MỚI] XEM LỊCH CỦA LỚP HỌC ===
    Route::get('/nguoihoc/lop-hoc/{id}/lich', [App\Http\Controllers\Web\LichHocWebController::class, 'showScheduleForClass'])
        ->name('nguoihoc.lophoc.schedule');

    
    // =====================================
});


// ===== KHU VỰC ADMIN =====
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth:admin'])->group(function () { 
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/giasu', GiaSuController::class);
        Route::resource('/nguoihoc', NguoiHocController::class);
        Route::resource('/lophoc', AdminLopHocController::class); // Dùng controller đã đổi tên
        Route::resource('/giaodich', GiaoDichController::class);
        Route::resource('/khieunai', KhieuNaiController::class);
    });
});