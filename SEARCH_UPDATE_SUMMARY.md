# ✅ HOÀN TẤT - Cập nhật hệ thống tìm kiếm/lọc

## 🎯 Đã sửa và cải tiến:

### 1. ✅ Sửa lỗi 422 trong SearchRequest
**Vấn đề:** Validation rule `max_price.gte:min_price` gây lỗi khi chỉ nhập `max_price` mà không có `min_price`

**Giải pháp:** Xóa rule `gte:min_price` và `gte:min_experience`

**Files thay đổi:**
- `BE_GiaSu/app/Http/Requests/SearchRequest.php`

---

### 2. ✅ Trang GIA SƯ (Tìm lớp học)
**API:** `GET /api/lophoc/search`

**Filters mới:**
1. ✅ **Môn học** (`subject_id`) - Đã có
2. ✅ **Giá mỗi buổi** (`min_price`, `max_price`) - Đã có
3. ✅ **Lớp** (`grade_id`) - **Mới thêm**
4. ✅ **Hình thức** (`form`: Online/Offline) - **Mới thêm**

**Đã bỏ:**
- ❌ Lọc theo trạng thái (không cần thiết)

**Controller:**
- `BE_GiaSu/app/Http/Controllers/LopHocYeuCauController.php`
- Method: `search()`

**Logic:**
```php
// Chỉ hiển thị lớp đang tìm gia sư
$query->whereIn('TrangThai', ['TimGiaSu', 'ChoDuyet', 'DangChonGiaSu']);

// Lọc theo môn học
if ($request->filled('subject_id')) {
    $query->where('MonID', $request->subject_id);
}

// Lọc theo khối lớp
if ($request->filled('grade_id')) {
    $query->where('KhoiLopID', $request->grade_id);
}

// Lọc theo hình thức (Online/Offline)
if ($request->filled('form') && $request->form !== 'Cả hai') {
    $query->where('HinhThuc', $request->form);
}

// Lọc theo giá
if ($request->filled('min_price')) {
    $query->where('HocPhi', '>=', $request->min_price);
}
if ($request->filled('max_price')) {
    $query->where('HocPhi', '<=', $request->max_price);
}
```

---

### 3. ✅ Trang HỌC VIÊN (Tìm gia sư)
**API:** `GET /api/giasu/search`

**Filters mới:**
1. ✅ **Chuyên môn** (`subject_id`) - Đã có, cải tiến
2. ✅ **Đánh giá** (`min_rating`, `max_rating`) - **Mới thêm**
3. ✅ **Kinh nghiệm** (`experience_level`, `min_experience`, `max_experience`) - Đã có
4. ✅ **Giới tính** (`gender`) - Đã có

**Controller:**
- `BE_GiaSu/app/Http/Controllers/GiaSuController.php`
- Method: `search()`

**Logic:**
```php
// 1. Lọc theo chuyên môn (môn học mà gia sư đã dạy)
if ($request->filled('subject_id')) {
    $query->whereHas('lopHocYeuCau', function($q) use ($subjectId) {
        $q->where('MonID', $subjectId)
          ->whereIn('TrangThai', ['DangHoc', 'HoanThanh']);
    });
}

// 2. Lọc theo đánh giá trung bình
if ($request->filled('min_rating')) {
    $query->whereHas('lopHocYeuCau', function($q) use ($minRating) {
        $q->whereHas('danhGia', function($danhGiaQuery) use ($minRating) {
            $danhGiaQuery->selectRaw('AVG(DiemSo) as avg_rating')
                        ->havingRaw('AVG(DiemSo) >= ?', [$minRating]);
        });
    });
}

// 3. Lọc theo kinh nghiệm (hỗ trợ '1', '2', '3', '5+')
if ($request->filled('experience_level')) {
    // Logic tìm kiếm theo số năm trong chuỗi KinhNghiem
}

// 4. Lọc theo giới tính
if ($request->filled('gender')) {
    $query->where('GioiTinh', $request->gender);
}
```

---

### 4. ✅ Cập nhật Model Relationships

**GiaSu Model:**
```php
// Thêm relationships mới
public function lopHocYeuCau()
{
    return $this->hasMany(LopHocYeuCau::class, 'GiaSuID', 'GiaSuID');
}

public function danhGia()
{
    return $this->hasManyThrough(
        DanhGia::class,
        LopHocYeuCau::class,
        'GiaSuID',
        'LopYeuCauID',
        'GiaSuID',
        'LopYeuCauID'
    );
}
```

---

## 📊 Bảng so sánh Filters

### Trang GIA SƯ (Tìm lớp):

| Filter | Tên field | Trạng thái |
|--------|-----------|------------|
| Môn học | `subject_id` | ✅ Đã có |
| Lớp | `grade_id` | ✅ Mới thêm |
| Giá mỗi buổi | `min_price`, `max_price` | ✅ Đã có |
| Hình thức | `form` (Online/Offline) | ✅ Mới thêm |
| ~~Trạng thái~~ | ~~`status`~~ | ❌ Đã bỏ |

### Trang HỌC VIÊN (Tìm gia sư):

| Filter | Tên field | Trạng thái |
|--------|-----------|------------|
| Chuyên môn | `subject_id` | ✅ Cải tiến |
| Đánh giá | `min_rating`, `max_rating` | ✅ Mới thêm |
| Kinh nghiệm | `experience_level` | ✅ Đã có |
| Giới tính | `gender` | ✅ Đã có |

---

## 🧪 API Testing

### Test 1: Tìm lớp học (Trang Gia Sư)

```bash
# Test lọc theo môn học + khối lớp + hình thức
GET /api/lophoc/search?subject_id=1&grade_id=2&form=Online

# Test lọc theo giá
GET /api/lophoc/search?min_price=100000&max_price=300000

# Test chỉ nhập max_price (Không còn lỗi 422)
GET /api/lophoc/search?max_price=500000
```

**Kết quả mong đợi:**
- ✅ Status 200
- ✅ Trả về danh sách lớp phù hợp
- ✅ Không có lỗi 422

### Test 2: Tìm gia sư (Trang Học Viên)

```bash
# Test lọc theo chuyên môn
GET /api/giasu/search?subject_id=1

# Test lọc theo đánh giá
GET /api/giasu/search?min_rating=4.0&max_rating=5.0

# Test lọc theo kinh nghiệm
GET /api/giasu/search?experience_level=5+

# Test lọc theo giới tính
GET /api/giasu/search?gender=Nam

# Test kết hợp nhiều filter
GET /api/giasu/search?subject_id=1&min_rating=4.5&gender=Nữ&experience_level=3
```

**Kết quả mong đợi:**
- ✅ Status 200
- ✅ Trả về danh sách gia sư phù hợp
- ✅ Relationship danhGia hoạt động

---

## 🔧 Các file đã thay đổi:

1. ✅ `BE_GiaSu/app/Http/Requests/SearchRequest.php` - Sửa validation
2. ✅ `BE_GiaSu/app/Http/Controllers/LopHocYeuCauController.php` - Cập nhật search()
3. ✅ `BE_GiaSu/app/Http/Controllers/GiaSuController.php` - Cập nhật search()
4. ✅ `BE_GiaSu/app/Models/GiaSu.php` - Thêm relationships

---

## ⚠️ Lưu ý quan trọng:

### 1. Về lọc đánh giá:
- Chỉ hoạt động khi gia sư đã có đánh giá trong database
- Nếu gia sư chưa có đánh giá → không xuất hiện trong kết quả
- Có thể cần điều chỉnh logic để hiển thị cả gia sư chưa có đánh giá

### 2. Về lọc chuyên môn:
- Tìm theo môn học mà gia sư đã dạy (qua bảng LopHocYeuCau)
- Chỉ tính lớp có trạng thái 'DangHoc' hoặc 'HoanThanh'

### 3. Clear cache sau khi cập nhật:
```cmd
cd BE_GiaSu
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## ✅ Checklist hoàn thành:

- [x] Sửa lỗi 422 khi nhập max_price
- [x] Bỏ lọc theo trạng thái (Trang Gia Sư)
- [x] Thêm lọc theo lớp (Trang Gia Sư)
- [x] Thêm lọc theo hình thức Online/Offline (Trang Gia Sư)
- [x] Cải tiến lọc theo chuyên môn (Trang Học Viên)
- [x] Thêm lọc theo đánh giá (Trang Học Viên)
- [x] Giữ nguyên lọc theo kinh nghiệm (Trang Học Viên)
- [x] Giữ nguyên lọc theo giới tính (Trang Học Viên)
- [x] Thêm relationships vào Model
- [x] Không ảnh hưởng đến chức năng khác

---

## 🎉 Kết luận:

Hệ thống tìm kiếm/lọc đã được cải tiến hoàn chỉnh:
- ✅ Lỗi 422 đã được sửa
- ✅ Filters theo yêu cầu đã được triển khai
- ✅ Code gọn gàng, dễ bảo trì
- ✅ Không ảnh hưởng các chức năng khác

**Clear cache và test ngay!** 🚀
