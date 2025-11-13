# API Admin Documentation

Tài liệu hướng dẫn sử dụng các API Admin cho hệ thống Gia sư - Người học.

## Tổng quan

Tất cả các API admin đều có prefix `/api/admin` và yêu cầu authentication thông qua Sanctum.

**Lưu ý:** Hiện tại chưa có middleware kiểm tra vai trò admin. Cần bổ sung middleware để đảm bảo chỉ admin mới truy cập được các route này.

---

## 1. Dashboard & Thống kê

### GET /api/admin/dashboard
Lấy dữ liệu tổng quan cho dashboard admin.

**Response:**
```json
{
  "_stats": [
    {
      "title": "Tổng Gia Sư",
      "count": 150,
      "percent": "+2.5%",
      "color": "text-success"
    }
  ],
  "revenueChartLabels": ["Tuần 40", "Tuần 41", ...],
  "revenueChartData": [12000000, 15000000, ...],
  "tongGiaSu": 150,
  "tongNguoiHoc": 320
}
```

---

## 2. Quản lý Gia sư

### GET /api/admin/giasu
Lấy danh sách tất cả gia sư (có phân trang).

**Query Parameters:**
- `search` (string): Tìm kiếm theo email, SĐT, hoặc họ tên
- `trangthai` (0|1): Lọc theo trạng thái tài khoản
- `page` (int): Số trang

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [...],
    "per_page": 10,
    "total": 150
  }
}
```

### GET /api/admin/giasu/pending
Lấy danh sách gia sư chờ duyệt (TrangThai = 0).

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [...]
  }
}
```

### GET /api/admin/giasu/{id}
Xem chi tiết thông tin gia sư.

### PUT /api/admin/giasu/{id}/approve
Duyệt hồ sơ gia sư (kích hoạt tài khoản).

**Response:**
```json
{
  "success": true,
  "message": "Duyệt hồ sơ gia sư thành công!",
  "data": {...}
}
```

### PUT /api/admin/giasu/{id}/reject
Từ chối hồ sơ gia sư (vô hiệu hóa tài khoản).

**Request Body:**
```json
{
  "ly_do": "Thông tin không hợp lệ"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Từ chối hồ sơ gia sư thành công!",
  "ly_do": "Thông tin không hợp lệ"
}
```

### POST /api/admin/giasu
Tạo mới gia sư.

**Request Body:**
```json
{
  "Email": "giasu@example.com",
  "SoDienThoai": "0912345678",
  "MatKhau": "password123",
  "MatKhau_confirmation": "password123",
  "TrangThai": 1,
  "HoTen": "Nguyễn Văn A",
  "GioiTinh": "Nam",
  "NgaySinh": "1990-01-01",
  "DiaChi": "123 Đường ABC",
  "BangCap": "Thạc sĩ Toán học",
  "TruongDaoTao": "ĐH Khoa học Tự nhiên",
  "ChuyenNganh": "Toán ứng dụng",
  "KinhNghiem": "5 năm",
  "ThanhTich": "Giải nhất Olympic Toán"
}
```

### PUT /api/admin/giasu/{id}
Cập nhật thông tin gia sư.

### DELETE /api/admin/giasu/{id}
Xóa gia sư.

---

## 3. Quản lý Người học

### GET /api/admin/nguoihoc
Lấy danh sách người học (có phân trang).

**Query Parameters:**
- `search` (string): Tìm kiếm theo email, SĐT, hoặc họ tên
- `trangthai` (0|1): Lọc theo trạng thái
- `page` (int): Số trang

### GET /api/admin/nguoihoc/{id}
Xem chi tiết người học.

### POST /api/admin/nguoihoc
Tạo mới người học.

### PUT /api/admin/nguoihoc/{id}
Cập nhật thông tin người học.

### DELETE /api/admin/nguoihoc/{id}
Xóa người học.

---

## 4. Quản lý Khiếu nại

### GET /api/admin/khieunai
Lấy danh sách khiếu nại (có phân trang).

**Query Parameters:**
- `search` (string): Tìm kiếm theo nội dung hoặc email
- `trangthai` (string): Lọc theo trạng thái (TiepNhan|DangXuLy|DaGiaiQuyet|TuChoi)
- `page` (int): Số trang

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "KhieuNaiID": 1,
        "NoiDung": "Gia sư không đến dạy",
        "TrangThai": "TiepNhan",
        "NgayTao": "2025-11-13 10:00:00",
        "taiKhoan": {...},
        "lop": {...}
      }
    ]
  }
}
```

### GET /api/admin/khieunai/statistics
Thống kê khiếu nại theo trạng thái.

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 45,
    "by_status": {
      "TiepNhan": 12,
      "DangXuLy": 8,
      "DaGiaiQuyet": 20,
      "TuChoi": 5
    }
  }
}
```

### GET /api/admin/khieunai/{id}
Xem chi tiết khiếu nại.

**Response:**
```json
{
  "success": true,
  "data": {
    "KhieuNaiID": 1,
    "NoiDung": "Gia sư không đến dạy",
    "TrangThai": "TiepNhan",
    "NgayTao": "2025-11-13 10:00:00",
    "GhiChu": null,
    "taiKhoan": {...},
    "lop": {...},
    "giaoDich": {...}
  }
}
```

### PUT /api/admin/khieunai/{id}/status
Cập nhật trạng thái khiếu nại.

**Request Body:**
```json
{
  "TrangThai": "DangXuLy",
  "GhiChu": "Đang liên hệ với gia sư để xác minh"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái khiếu nại thành công!",
  "data": {...}
}
```

### DELETE /api/admin/khieunai/{id}
Xóa khiếu nại.

---

## 5. Quản lý Giao dịch

### GET /api/admin/giaodich
Lấy danh sách giao dịch (có phân trang).

**Query Parameters:**
- `search` (string): Tìm kiếm theo ghi chú hoặc email
- `trangthai` (string): Lọc theo trạng thái (ChoXuLy|ThanhCong|ThatBai|HoanTien)
- `tu_ngay` (date): Lọc từ ngày (YYYY-MM-DD)
- `den_ngay` (date): Lọc đến ngày (YYYY-MM-DD)
- `min_amount` (number): Số tiền tối thiểu
- `max_amount` (number): Số tiền tối đa
- `page` (int): Số trang

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "GiaoDichID": 1,
        "SoTien": 500000,
        "ThoiGian": "2025-11-13 10:00:00",
        "TrangThai": "ThanhCong",
        "GhiChu": "Thanh toán học phí tháng 11",
        "taiKhoan": {...},
        "lop": {...}
      }
    ]
  }
}
```

### GET /api/admin/giaodich/statistics
Thống kê giao dịch chi tiết.

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total_transactions": 350,
      "total_amount": 175000000,
      "success_amount": 165000000
    },
    "by_status": [
      {
        "TrangThai": "ThanhCong",
        "count": 320,
        "total": 165000000
      }
    ],
    "by_month": [...],
    "by_week": [...]
  }
}
```

### GET /api/admin/giaodich/export
Xuất báo cáo giao dịch (JSON).

**Query Parameters:** (giống như GET /api/admin/giaodich)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "GiaoDichID": 1,
      "Email": "user@example.com",
      "SoTien": 500000,
      "ThoiGian": "2025-11-13 10:00:00",
      "TrangThai": "ThanhCong",
      "GhiChu": "...",
      "LopYeuCauID": 1
    }
  ],
  "total": 350
}
```

### GET /api/admin/giaodich/{id}
Xem chi tiết giao dịch.

### PUT /api/admin/giaodich/{id}/status
Cập nhật trạng thái giao dịch.

**Request Body:**
```json
{
  "TrangThai": "ThanhCong",
  "GhiChu": "Đã xác nhận thanh toán"
}
```

### DELETE /api/admin/giaodich/{id}
Xóa giao dịch (cẩn thận khi sử dụng).

---

## 6. Quản lý Tài khoản

### GET /api/admin/taikhoan
Lấy danh sách tất cả tài khoản (có phân trang).

**Response:**
```json
{
  "taiKhoans": {
    "data": [...]
  }
}
```

---

## Các trạng thái (Status)

### Trạng thái Tài khoản:
- `0`: Chưa kích hoạt / Chờ duyệt
- `1`: Đã kích hoạt

### Trạng thái Khiếu nại:
- `TiepNhan`: Mới tiếp nhận
- `DangXuLy`: Đang xử lý
- `DaGiaiQuyet`: Đã giải quyết
- `TuChoi`: Từ chối xử lý

### Trạng thái Giao dịch:
- `ChoXuLy`: Chờ xử lý
- `ThanhCong`: Thanh toán thành công
- `ThatBai`: Thanh toán thất bại
- `HoanTien`: Đã hoàn tiền

---

## Middleware cần bổ sung

Để đảm bảo bảo mật, cần tạo middleware kiểm tra vai trò admin:

### Tạo file: `app/Http/Middleware/CheckAdminRole.php`

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Kiểm tra vai trò (VaiTroID = 1 là Admin)
        $phanQuyen = $user->phanquyen;
        if (!$phanQuyen || $phanQuyen->VaiTroID !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        return $next($request);
    }
}
```

### Đăng ký middleware trong `bootstrap/app.php` hoặc `app/Http/Kernel.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\CheckAdminRole::class,
    ]);
})
```

### Áp dụng middleware vào routes/api.php

```php
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Tất cả routes admin ở đây
});
```

---

## Checklist Hoàn thành

✅ **Quản lý tài khoản gia sư**: CRUD, duyệt hồ sơ, từ chối hồ sơ
✅ **Quản lý tài khoản người học**: CRUD
✅ **Duyệt hồ sơ gia sư**: approveProfile, rejectProfile, danh sách chờ duyệt
✅ **Quản lý khiếu nại**: CRUD, cập nhật trạng thái, thống kê
✅ **Quản lý báo cáo và thống kê**: Dashboard với biểu đồ, thống kê giao dịch, khiếu nại
✅ **Quản lý giao dịch**: CRUD, thống kê, xuất báo cáo

⚠️ **Cần bổ sung**: Middleware kiểm tra vai trò admin
