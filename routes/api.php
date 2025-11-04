<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiaSuController;
use App\Http\Controllers\NguoiHocController;
use App\Http\Controllers\LopHocYeuCauController;
use App\Http\Controllers\YeuCauNhanLopController;
use App\Http\Controllers\LichHocController;
use App\Http\Controllers\DropdownDataController;


Route::get('/giasu/{giaSuID}/lop', [YeuCauNhanLopController::class, 'getLopCuaGiaSu']);

Route::post('/resetpassword', [AuthController::class, 'resetPassword']);
Route::post('/giasu/guiyeucau', [YeuCauNhanLopController::class, 'giaSuGuiYeuCau']);
Route::post('/nguoihoc/moigiasu', [YeuCauNhanLopController::class, 'nguoiHocMoiGiaSu']);
Route::put('/yeucau/{yeuCauID}', [YeuCauNhanLopController::class, 'capNhatYeuCau']);
Route::put('/yeucau/{yeuCauID}/xacnhan', [YeuCauNhanLopController::class, 'xacNhanYeuCau']);
Route::put('/yeucau/{yeuCauID}/tuchoi', [YeuCauNhanLopController::class, 'tuChoiYeuCau']);
Route::delete('/yeucau/{yeuCauID}/huy', [YeuCauNhanLopController::class, 'huyYeuCau']);
Route::get('/yeucau/dagui', [YeuCauNhanLopController::class, 'danhSachYeuCauDaGui']);
Route::get('/yeucau/nhanduoc', [YeuCauNhanLopController::class, 'danhSachYeuCauNhanDuoc']);
Route::get('/lophocyeucau/{lopYeuCauID}/de-nghi', [YeuCauNhanLopController::class, 'danhSachDeNghiTheoLop']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::put('/lophocyeucau/{id}', [LopHocYeuCauController::class, 'update']);

// ⚠️ Đặt route này TRƯỚC resource
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/nguoihoc/lopcuatoi', [NguoiHocController::class, 'getLopHocCuaNguoiHoc']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('/changepassword', [AuthController::class, 'changePassword']);
});

// === API MỚI CHO SEARCH & FILTER (PHẢI ĐẶT TRƯỚC RESOURCE ROUTES) ===
Route::get('/giasu/search', [GiaSuController::class, 'search']);
Route::get('/lophoc/search', [LopHocYeuCauController::class, 'search']);
Route::get('/filter-options', [DropdownDataController::class, 'getFilterOptions']);
Route::get('/search-stats', [DropdownDataController::class, 'getSearchStats']);
Route::get('/search-suggestions', [DropdownDataController::class, 'getSearchSuggestions']);

Route::resource('nguoihoc', NguoiHocController::class); // 👈 đặt sau
Route::resource('giasu', GiaSuController::class);
Route::resource('lophocyeucau', LopHocYeuCauController::class);

// === API MỚI CHO DROPDOWN ===
Route::get('/monhoc', [DropdownDataController::class, 'getMonHocList']);
Route::get('/khoilop', [DropdownDataController::class, 'getKhoiLopList']);
Route::get('/doituong', [DropdownDataController::class, 'getDoiTuongList']);
Route::get('/thoigianday', [DropdownDataController::class, 'getThoiGianDayList']);

Route::controller(LichHocController::class)->prefix('lich-hoc')->group(function () {
        Route::post('/', 'taoLichHoc');
        Route::put('/{lichHocId}', 'capNhatLichHoc');
        Route::delete('/{lichHocId}', 'xoaLichHoc');
    });