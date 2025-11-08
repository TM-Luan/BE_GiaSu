# 🔍 Quick Guide - Hệ thống Tìm kiếm mới

## ✅ Đã sửa:
- ✅ Lỗi 422 khi chỉ nhập `max_price` mà không có `min_price`
- ✅ Bỏ lọc theo trạng thái (không cần thiết)
- ✅ Thêm filters mới theo yêu cầu

---

## 📋 API Endpoints

### 1. Trang GIA SƯ - Tìm lớp học
**URL:** `GET /api/lophoc/search`

**Filters hỗ trợ:**
```
?subject_id=1           # Lọc theo môn học
&grade_id=2            # Lọc theo lớp (MỚI)
&form=Online           # Lọc Online/Offline (MỚI)
&min_price=100000      # Giá tối thiểu
&max_price=300000      # Giá tối đa (KHÔNG còn lỗi 422)
&keyword=toán          # Tìm kiếm từ khóa
```

### 2. Trang HỌC VIÊN - Tìm gia sư
**URL:** `GET /api/giasu/search`

**Filters hỗ trợ:**
```
?subject_id=1          # Lọc theo chuyên môn
&min_rating=4.0        # Đánh giá tối thiểu (MỚI)
&max_rating=5.0        # Đánh giá tối đa (MỚI)
&experience_level=5+   # Kinh nghiệm (1, 2, 3, 5+)
&gender=Nam            # Giới tính (Nam/Nữ/Khác)
&keyword=Nguyễn        # Tìm kiếm tên
```

---

## 🔧 Sau khi pull code:

```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🧪 Test nhanh:

### Test 1: Lỗi 422 đã fix chưa?
```bash
# Trước: Lỗi 422
# Sau: OK 200
GET /api/lophoc/search?max_price=500000
```

### Test 2: Lọc lớp học (Gia Sư)
```bash
GET /api/lophoc/search?subject_id=1&form=Online&grade_id=2
```

### Test 3: Lọc gia sư (Học Viên)
```bash
GET /api/giasu/search?subject_id=1&min_rating=4.0&gender=Nữ
```

---

## 📂 Files đã thay đổi:

1. `app/Http/Requests/SearchRequest.php` - Fix validation
2. `app/Http/Controllers/LopHocYeuCauController.php` - Filters mới
3. `app/Http/Controllers/GiaSuController.php` - Filters mới + đánh giá
4. `app/Models/GiaSu.php` - Thêm relationships

---

## ⚠️ Chú ý:

- ✅ Không ảnh hưởng đến chức năng khác (lịch học, đánh giá, v.v.)
- ✅ Chỉ sửa phần tìm kiếm/lọc
- ⚠️ Lọc đánh giá chỉ hiển thị gia sư đã có đánh giá

---

**Chi tiết đầy đủ:** Xem file `SEARCH_UPDATE_SUMMARY.md`
