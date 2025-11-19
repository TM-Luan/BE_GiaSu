<?php

use Illuminate\Support\Facades\Route;

// Web Controllers (Blade Views)
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\GiaSuDashboardController;
use App\Http\Controllers\Web\GiaSuLopHocController;
use App\Http\Controllers\Web\NguoiHocDashboardController;
use App\Http\Controllers\Web\LopHocController;
use App\Http\Controllers\Web\LichHocWebController;
use App\Http\Controllers\Web\ProfileController;

// Admin Web Controllers
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GiaSuController as AdminGiaSuController;
use App\Http\Controllers\Admin\NguoiHocController as AdminNguoiHocController;
use App\Http\Controllers\Admin\LopHocController as AdminLopHocController;
use App\Http\Controllers\Admin\GiaoDichController as AdminGiaoDichController;
use App\Http\Controllers\Admin\KhieuNaiController as AdminKhieuNaiController;


/*
|--------------------------------------------------------------------------
| Web Routes (Blade Views & Form Submissions)
|--------------------------------------------------------------------------
|
| Tất cả routes ở đây render Blade views hoặc xử lý form submissions
| Không dùng cho API endpoints
|
*/

// ===== PUBLIC ROUTES =====

// Homepage redirect
Route::get('/', fn() => redirect()->route('login'));

// Authentication
Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register.post');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


// ===== AUTHENTICATED USER ROUTES =====
Route::middleware(['auth'])->group(function () {
    
    // ===== GIA SƯ (TUTOR) ROUTES =====
    Route::prefix('giasu')->name('giasu.')->group(function () {
        // Dashboard - Browse available classes marketplace
        Route::get('/dashboard', [GiaSuDashboardController::class, 'index'])->name('dashboard');
        
        // Gửi đề nghị dạy lớp học
        Route::post('/de-nghi-day', [GiaSuDashboardController::class, 'deNghiDay'])->name('de_nghi_day');
        
        // Lớp học của tôi (các lớp đã được chấp nhận)
        Route::prefix('lop-hoc')->name('lophoc.')->group(function () {
            Route::get('/', [GiaSuLopHocController::class, 'index'])->name('index');
            Route::get('/{id}', [GiaSuLopHocController::class, 'show'])->name('show');
            
            // Xem lịch học của lớp
            Route::get('/{id}/lich-hoc', [GiaSuLopHocController::class, 'schedule'])->name('schedule');
            // Thêm lịch học mới
            Route::get('/{id}/lich-hoc/them', [GiaSuLopHocController::class, 'addSchedule'])->name('schedule.add');
            
            // Chấp nhận/từ chối lời mời từ học viên
            Route::post('/loi-moi/{yeuCauId}/chap-nhan', [GiaSuLopHocController::class, 'acceptInvitation'])->name('invitation.accept');
            Route::post('/loi-moi/{yeuCauId}/tu-choi', [GiaSuLopHocController::class, 'rejectInvitation'])->name('invitation.reject');
            
            // Hủy đề nghị đã gửi
            Route::post('/de-nghi/{yeuCauId}/huy', [GiaSuLopHocController::class, 'cancelProposal'])->name('proposal.cancel');
        });
        
        // Lịch học
        Route::get('/lich-hoc', [LichHocWebController::class, 'tutorSchedule'])->name('lichhoc.index');
        
        // Thông tin cá nhân
        Route::get('/thong-tin-ca-nhan', [ProfileController::class, 'tutorProfile'])->name('profile.index');
        Route::put('/thong-tin-ca-nhan', [ProfileController::class, 'tutorProfileUpdate'])->name('profile.update');
    });

    // ===== NGƯỜI HỌC (STUDENT) ROUTES =====
    Route::prefix('nguoihoc')->name('nguoihoc.')->group(function () {
        // Dashboard - Browse tutors marketplace
        Route::get('/', [GiaSuDashboardController::class, 'myClasses'])->name('index');
        Route::get('/dashboard', [NguoiHocDashboardController::class, 'index'])->name('dashboard');
        
        // Xem hồ sơ gia sư
        Route::get('/giasu/ho-so/{id}', [NguoiHocDashboardController::class, 'show'])->name('giasu.show');
        
        // Mời gia sư dạy
        Route::post('/moi-day', [NguoiHocDashboardController::class, 'moiDay'])->name('moi_day');

        // Quản lý lớp học của tôi
        Route::prefix('lop-hoc')->name('lophoc.')->group(function () {
            Route::get('/', [LopHocController::class, 'index'])->name('index');
            Route::get('/tao-moi', [LopHocController::class, 'create'])->name('create');
            Route::post('/', [LopHocController::class, 'store'])->name('store');
            Route::get('/{id}', [LopHocController::class, 'show'])->name('show');
            Route::get('/{id}/sua', [LopHocController::class, 'edit'])->name('edit');
            Route::put('/{id}', [LopHocController::class, 'update'])->name('update');
            Route::post('/{id}/huy', [LopHocController::class, 'cancel'])->name('cancel');
            Route::delete('/{id}', [LopHocController::class, 'destroy'])->name('destroy');
            
            // Xem danh sách đề nghị của lớp học
            Route::get('/{id}/de-nghi', [LopHocController::class, 'showProposals'])->name('proposals');
            
            // Xem lịch học của lớp
            Route::get('/{id}/lich', [LichHocWebController::class, 'showScheduleForClass'])->name('schedule');
            
            // Khiếu nại lớp học
            Route::get('/{id}/khieu-nai', [LopHocController::class, 'createComplaint'])->name('complaint.create');
            Route::post('/{id}/khieu-nai', [LopHocController::class, 'storeComplaint'])->name('complaint.store');
        });

        // Xử lý đề nghị (chấp nhận/từ chối gia sư)
        Route::post('/de-nghi/{yeuCauId}/chap-nhan', [LopHocController::class, 'acceptProposal'])->name('proposals.accept');
        Route::post('/de-nghi/{yeuCauId}/tu-choi', [LopHocController::class, 'rejectProposal'])->name('proposals.reject');
        
        // Lịch học
        Route::get('/lich-hoc', [LichHocWebController::class, 'index'])->name('lichhoc.index');
        
        // Thông tin cá nhân
        Route::get('/thong-tin-ca-nhan', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/thong-tin-ca-nhan', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/doi-mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
});


// ===== ADMIN WEB ROUTES =====
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Admin Protected Routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Resource Management
        Route::resource('/giasu', AdminGiaSuController::class);
        Route::resource('/nguoihoc', AdminNguoiHocController::class);
        Route::resource('/lophoc', AdminLopHocController::class);
        Route::resource('/giaodich', AdminGiaoDichController::class);
        Route::resource('/khieunai', AdminKhieuNaiController::class);
    });
});