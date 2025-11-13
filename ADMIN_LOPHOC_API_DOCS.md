# API Admin - Quản lý Lớp học & Lịch học

Tài liệu API cho quản lý Lớp học (Khóa học) và Lịch học trong hệ thống Admin.

---

## 1. Quản lý Lớp học (Khóa học)

### GET /api/admin/lophoc
Lấy danh sách tất cả lớp học (có phân trang và bộ lọc).

**Query Parameters:**
- `search` (string): Tìm kiếm theo mô tả, tên người học, tên gia sư
- `trangthai` (string): Lọc theo trạng thái (DangMo|DangDay|HoanThanh|DaHuy)
- `hinhthuc` (string): Lọc theo hình thức (Online|Offline)
- `mon_id` (int): Lọc theo môn học
- `khoi_id` (int): Lọc theo khối lớp
- `page` (int): Số trang

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "LopYeuCauID": 1,
        "NguoiHocID": 5,
        "GiaSuID": 3,
        "HinhThuc": "Online",
        "HocPhi": 500000,
        "ThoiLuong": 90,
        "TrangThai": "DangDay",
        "SoLuong": 1,
        "MoTa": "Học Toán lớp 12",
        "nguoiHoc": {
          "HoTen": "Nguyễn Văn A",
          "taiKhoan": {
            "Email": "nguyenvana@example.com"
          }
        },
        "giaSu": {
          "HoTen": "Trần Thị B",
          "taiKhoan": {
            "Email": "tranthib@example.com"
          }
        },
        "monHoc": {
          "TenMon": "Toán"
        }
      }
    ],
    "per_page": 15,
    "total": 150
  }
}
```

### GET /api/admin/lophoc/statistics
Thống kê tổng quan về lớp học.

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total": 150,
      "avg_fee": 450000,
      "total_completed_fee": 25000000
    },
    "by_status": {
      "DangMo": 30,
      "DangDay": 45,
      "HoanThanh": 60,
      "DaHuy": 15
    },
    "by_form": {
      "Online": 80,
      "Offline": 70
    },
    "by_subject": [
      {
        "MonHoc": "Toán",
        "SoLop": 45
      },
      {
        "MonHoc": "Tiếng Anh",
        "SoLop": 38
      }
    ]
  }
}
```

### GET /api/admin/lophoc/{id}
Xem chi tiết lớp học.

**Response:**
```json
{
  "success": true,
  "data": {
    "LopYeuCauID": 1,
    "NguoiHocID": 5,
    "GiaSuID": 3,
    "HinhThuc": "Online",
    "HocPhi": 500000,
    "ThoiLuong": 90,
    "TrangThai": "DangDay",
    "nguoiHoc": {...},
    "giaSu": {...},
    "monHoc": {...},
    "lichHoc": [...],
    "danhGia": [...]
  }
}
```

### PUT /api/admin/lophoc/{id}
Cập nhật thông tin lớp học.

**Request Body:**
```json
{
  "HocPhi": 600000,
  "ThoiLuong": 120,
  "SoLuong": 2,
  "MoTa": "Học Toán lớp 12 - Ôn thi THPT",
  "HinhThuc": "Online",
  "TrangThai": "DangDay"
}
```

### PUT /api/admin/lophoc/{id}/status
Cập nhật trạng thái lớp học.

**Request Body:**
```json
{
  "TrangThai": "HoanThanh",
  "GhiChu": "Đã hoàn thành khóa học"
}
```

### DELETE /api/admin/lophoc/{id}
Xóa lớp học (chỉ xóa được lớp chưa có lịch học).

**Response:**
```json
{
  "success": true,
  "message": "Xóa lớp học thành công!"
}
```

### GET /api/admin/lophoc/giasu/{giaSuId}
Lấy danh sách lớp của một gia sư.

### GET /api/admin/lophoc/nguoihoc/{nguoiHocId}
Lấy danh sách lớp của một người học.

---

## 2. Quản lý Lịch học

### GET /api/admin/lichhoc
Lấy danh sách lịch học (có phân trang và bộ lọc).

**Query Parameters:**
- `trangthai` (string): Lọc theo trạng thái (ChuaBatDau|DangHoc|HoanThanh|DaHuy)
- `lop_id` (int): Lọc theo lớp học
- `tu_ngay` (date): Lọc từ ngày (YYYY-MM-DD)
- `den_ngay` (date): Lọc đến ngày (YYYY-MM-DD)
- `thang` (int): Lọc theo tháng (1-12)
- `nam` (int): Lọc theo năm (2020+)
- `page` (int): Số trang

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "LichHocID": 1,
        "LopYeuCauID": 5,
        "NgayHoc": "2025-11-15",
        "ThoiGianBatDau": "14:00:00",
        "ThoiGianKetThuc": "15:30:00",
        "TrangThai": "ChuaBatDau",
        "DuongDan": "https://meet.google.com/abc-defg-hij",
        "lopHocYeuCau": {
          "nguoiHoc": {...},
          "giaSu": {...},
          "monHoc": {...}
        }
      }
    ],
    "per_page": 20,
    "total": 350
  }
}
```

### GET /api/admin/lichhoc/statistics
Thống kê lịch học.

**Query Parameters:**
- `thang` (int): Tháng cần thống kê
- `nam` (int): Năm cần thống kê

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total": 350
    },
    "by_status": {
      "ChuaBatDau": 80,
      "DangHoc": 20,
      "HoanThanh": 220,
      "DaHuy": 30
    },
    "by_day_of_week": [
      {
        "day": "Thứ 2",
        "count": 65
      },
      {
        "day": "Thứ 3",
        "count": 58
      }
    ],
    "upcoming": [
      {
        "LichHocID": 125,
        "NgayHoc": "2025-11-14",
        "ThoiGianBatDau": "14:00:00",
        "lopHocYeuCau": {...}
      }
    ]
  }
}
```

### GET /api/admin/lichhoc/calendar
Lấy lịch học dạng calendar (theo tháng).

**Query Parameters:**
- `thang` (int, required): Tháng (1-12)
- `nam` (int, required): Năm (2020+)

**Response:**
```json
{
  "success": true,
  "data": {
    "2025-11-14": [
      {
        "LichHocID": 1,
        "NgayHoc": "2025-11-14",
        "ThoiGianBatDau": "08:00:00",
        "lopHocYeuCau": {...}
      },
      {
        "LichHocID": 2,
        "NgayHoc": "2025-11-14",
        "ThoiGianBatDau": "14:00:00",
        "lopHocYeuCau": {...}
      }
    ],
    "2025-11-15": [...]
  }
}
```

### GET /api/admin/lichhoc/{id}
Xem chi tiết lịch học.

**Response:**
```json
{
  "success": true,
  "data": {
    "LichHocID": 1,
    "LopYeuCauID": 5,
    "NgayHoc": "2025-11-15",
    "ThoiGianBatDau": "14:00:00",
    "ThoiGianKetThuc": "15:30:00",
    "TrangThai": "ChuaBatDau",
    "DuongDan": "https://meet.google.com/abc-defg-hij",
    "IsLapLai": true,
    "lopHocYeuCau": {...},
    "lichHocGoc": null,
    "lichHocCon": [...]
  }
}
```

### PUT /api/admin/lichhoc/{id}
Cập nhật thông tin lịch học.

**Request Body:**
```json
{
  "NgayHoc": "2025-11-16",
  "ThoiGianBatDau": "15:00:00",
  "ThoiGianKetThuc": "16:30:00",
  "DuongDan": "https://meet.google.com/new-link",
  "TrangThai": "ChuaBatDau"
}
```

### PUT /api/admin/lichhoc/{id}/status
Cập nhật trạng thái lịch học.

**Request Body:**
```json
{
  "TrangThai": "HoanThanh"
}
```

### DELETE /api/admin/lichhoc/{id}
Xóa lịch học.

**Note:** Nếu xóa lịch gốc có lịch con (lặp lại), cần gửi `confirm_delete_all=true` để xóa tất cả.

**Request (nếu cần xác nhận):**
```
DELETE /api/admin/lichhoc/1?confirm_delete_all=true
```

### GET /api/admin/lichhoc/lop/{lopId}
Lấy tất cả lịch học của một lớp.

---

## Trạng thái

### Trạng thái Lớp học:
- `DangMo`: Đang mở (chưa có gia sư)
- `DangDay`: Đang dạy (đã có gia sư, đang học)
- `HoanThanh`: Đã hoàn thành
- `DaHuy`: Đã hủy

### Trạng thái Lịch học:
- `ChuaBatDau`: Chưa bắt đầu
- `DangHoc`: Đang học (buổi học đang diễn ra)
- `HoanThanh`: Đã hoàn thành
- `DaHuy`: Đã hủy

---

## Use Cases

### 1. Admin xem tổng quan lớp học
```bash
GET /api/admin/lophoc?page=1
GET /api/admin/lophoc/statistics
```

### 2. Admin tìm kiếm lớp học theo gia sư
```bash
GET /api/admin/lophoc/giasu/3
```

### 3. Admin xem lịch dạy trong tháng
```bash
GET /api/admin/lichhoc/calendar?thang=11&nam=2025
```

### 4. Admin cập nhật trạng thái lớp hoàn thành
```bash
PUT /api/admin/lophoc/5/status
Body: {"TrangThai": "HoanThanh"}
```

### 5. Admin xem lịch học sắp tới (7 ngày)
```bash
GET /api/admin/lichhoc/statistics
```

### 6. Admin lọc lịch học theo trạng thái
```bash
GET /api/admin/lichhoc?trangthai=ChuaBatDau&tu_ngay=2025-11-13&den_ngay=2025-11-20
```

---

## Tích hợp với chức năng khác

### Liên kết với Khiếu nại
Khi xem chi tiết lớp học, có thể xem khiếu nại liên quan:
```bash
GET /api/admin/khieunai?search=LopYeuCauID:5
```

### Liên kết với Giao dịch
Xem giao dịch thanh toán của lớp:
```bash
GET /api/admin/giaodich?search=LopYeuCauID:5
```

### Liên kết với Gia sư/Người học
Xem tất cả lớp của gia sư/người học:
```bash
GET /api/admin/lophoc/giasu/3
GET /api/admin/lophoc/nguoihoc/5
```
