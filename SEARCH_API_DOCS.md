# API Documentation - Search & Filter Features

## Endpoints Overview

### 1. Search Tutors
**GET** `/api/giasu/search`

Tìm kiếm và lọc danh sách gia sư với nhiều tiêu chí khác nhau.

#### Parameters:
- `keyword` (string, optional): Tìm kiếm theo tên gia sư
- `gender` (string, optional): Lọc theo giới tính (`Nam`, `Nữ`)
- `education_level` (string, optional): Lọc theo trình độ học vấn
- `experience_level` (string, optional): Lọc theo kinh nghiệm (`beginner`, `intermediate`, `experienced`, `expert`)
- `subject_id` (integer, optional): Lọc theo môn học
- `grade_id` (integer, optional): Lọc theo khối lớp
- `min_price` (numeric, optional): Giá tối thiểu
- `max_price` (numeric, optional): Giá tối đa
- `sort_by` (string, optional): Sắp xếp theo (`name`, `experience`, `created_at`)
- `sort_order` (string, optional): Thứ tự sắp xếp (`asc`, `desc`)
- `per_page` (integer, optional): Số lượng kết quả mỗi trang (default: 20, max: 100)
- `page` (integer, optional): Trang hiện tại (default: 1)

#### Example Request:
```bash
GET /api/giasu/search?keyword=Nguyễn&gender=Nữ&experience_level=experienced&min_price=200000&max_price=500000&sort_by=name&sort_order=asc&per_page=10
```

#### Example Response:
```json
{
    "success": true,
    "data": [
        {
            "GiaSuID": 1,
            "HoTen": "Nguyễn Thị A",
            "DiaChi": "Hà Nội",
            "GioiTinh": "Nữ",
            "BangCap": "Thạc sĩ",
            "KinhNghiem": "3 năm kinh nghiệm",
            "AnhDaiDien": null,
            "taiKhoan": {...}
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 10,
        "total": 25,
        "from": 1,
        "to": 10
    }
}
```

---

### 2. Search Classes
**GET** `/api/lophoc/search`

Tìm kiếm và lọc danh sách lớp học cần tìm gia sư.

#### Parameters:
- `keyword` (string, optional): Tìm kiếm trong mô tả hoặc tên người học
- `subject_id` (integer, optional): Lọc theo môn học
- `grade_id` (integer, optional): Lọc theo khối lớp
- `target_id` (integer, optional): Lọc theo đối tượng
- `time_id` (integer, optional): Lọc theo thời gian dạy
- `form` (string, optional): Lọc theo hình thức (`Online`, `Offline`, `Cả hai`)
- `status` (string, optional): Lọc theo trạng thái (`TimGiaSu`, `ChoDuyet`, `DangHoc`, `HoanThanh`)
- `location` (string, optional): Lọc theo địa chỉ
- `min_price` (numeric, optional): Học phí tối thiểu
- `max_price` (numeric, optional): Học phí tối đa
- `sort_by` (string, optional): Sắp xếp theo (`price`, `duration`, `students`, `created_at`)
- `sort_order` (string, optional): Thứ tự sắp xếp (`asc`, `desc`)
- `per_page` (integer, optional): Số lượng kết quả mỗi trang
- `page` (integer, optional): Trang hiện tại

#### Example Request:
```bash
GET /api/lophoc/search?subject_id=1&form=Online&min_price=150000&max_price=300000&sort_by=price&sort_order=asc
```

---

### 3. Get Filter Options
**GET** `/api/filter-options`

Lấy tất cả dữ liệu dropdown để hiển thị trong form filter.

#### Response:
```json
{
    "success": true,
    "data": {
        "subjects": [
            {"MonID": 1, "TenMon": "Toán"},
            {"MonID": 2, "TenMon": "Lý"}
        ],
        "grades": [
            {"KhoiLopID": 1, "BacHoc": "Lớp 6"},
            {"KhoiLopID": 2, "BacHoc": "Lớp 7"}
        ],
        "targets": [
            {"DoiTuongID": 1, "TenDoiTuong": "Học sinh"},
            {"DoiTuongID": 2, "TenDoiTuong": "Sinh viên"}
        ],
        "times": [
            {"ThoiGianDayID": 1, "SoBuoi": 2, "BuoiHoc": "Sáng"},
            {"ThoiGianDayID": 2, "SoBuoi": 3, "BuoiHoc": "Chiều"}
        ],
        "education_levels": [
            {"value": "Sinh viên", "label": "Sinh viên"},
            {"value": "Cử nhân", "label": "Cử nhân"}
        ],
        "experience_levels": [
            {"value": "beginner", "label": "Mới bắt đầu (0-1 năm)"},
            {"value": "intermediate", "label": "Trung bình (1-3 năm)"}
        ],
        "genders": [
            {"value": "Nam", "label": "Nam"},
            {"value": "Nữ", "label": "Nữ"}
        ],
        "forms": [
            {"value": "Online", "label": "Online"},
            {"value": "Offline", "label": "Tại nhà"}
        ],
        "class_statuses": [
            {"value": "TimGiaSu", "label": "Đang tìm gia sư"},
            {"value": "ChoDuyet", "label": "Chờ duyệt"}
        ],
        "sort_options": {
            "tutor_sort": [
                {"value": "name", "label": "Tên A-Z"},
                {"value": "experience", "label": "Kinh nghiệm"}
            ],
            "class_sort": [
                {"value": "price", "label": "Học phí"},
                {"value": "duration", "label": "Thời lượng"}
            ]
        },
        "price_ranges": [
            {"min": 0, "max": 100000, "label": "Dưới 100k"},
            {"min": 100000, "max": 200000, "label": "100k - 200k"}
        ]
    }
}
```

---

### 4. Get Search Statistics
**GET** `/api/search-stats`

Lấy thống kê tổng quan về gia sư và lớp học.

#### Response:
```json
{
    "success": true,
    "data": {
        "total_tutors": 150,
        "total_classes": 89,
        "total_subjects": 12,
        "avg_price": 275000,
        "price_range": {
            "min": 50000,
            "max": 800000
        }
    }
}
```

---

### 5. Get Search Suggestions
**GET** `/api/search-suggestions`

Lấy gợi ý tìm kiếm tự động (autocomplete).

#### Parameters:
- `q` (string, required): Từ khóa tìm kiếm
- `type` (string, optional): Loại gợi ý (`tutors`, `classes`, `all`)

#### Example Request:
```bash
GET /api/search-suggestions?q=Nguyễn&type=tutors
```

#### Example Response:
```json
{
    "success": true,
    "data": {
        "tutors": [
            {"type": "tutor", "text": "Nguyễn Văn A"},
            {"type": "tutor", "text": "Nguyễn Thị B"}
        ],
        "classes": [
            {"type": "class", "text": "Lớp toán 12 cần gia sư..."}
        ]
    }
}
```

---

## Testing Commands

### 1. Test Filter Options
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/filter-options" -Method GET -Headers @{"Accept"="application/json"}
```

### 2. Test Search Tutors
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/giasu/search?keyword=Nguyễn&gender=Nữ" -Method GET -Headers @{"Accept"="application/json"}
```

### 3. Test Search Classes
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/lophoc/search?min_price=100000&max_price=300000" -Method GET -Headers @{"Accept"="application/json"}
```

### 4. Test Search Stats
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/search-stats" -Method GET -Headers @{"Accept"="application/json"}
```

### 5. Test Search Suggestions
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/search-suggestions?q=toán&type=classes" -Method GET -Headers @{"Accept"="application/json"}
```

---

## Error Handling

All APIs return standardized error responses:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

Common HTTP status codes:
- `200` - Success
- `400` - Bad Request (validation errors)
- `500` - Internal Server Error

---

## Features Implemented

✅ **Advanced Search**
- Keyword search in multiple fields
- Multiple filter criteria combination
- Flexible sorting options

✅ **Pagination**
- Configurable page size
- Complete pagination metadata

✅ **Validation**
- Input validation with custom messages
- Type checking and range validation

✅ **Performance**
- Efficient database queries with relationships
- Indexed searches

✅ **User Experience**
- Autocomplete suggestions
- Filter options loading
- Statistics for overview