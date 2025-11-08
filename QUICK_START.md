# 🚀 HƯỚNG DẪN NHANH - Import Database

## ✅ Đã hoàn tất:
- ✅ Cả 2 file `sql.sql` và `railway.sql` đã có cột `LanSua INT NOT NULL DEFAULT 0`
- ✅ Dữ liệu mẫu đã bao gồm giá trị `LanSua = 0`
- ✅ Backend `DanhGiaController.php` đã xử lý logic chặn
- ✅ Frontend `tutor_detail_page.dart` đã có dialog cảnh báo

---

## 📦 Bạn cần làm GÌ?

### Chọn 1 trong 2 cách:

#### 🔴 Cách 1: Import toàn bộ database (Xóa data cũ)

**Local MySQL:**
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
mysql -u root -p -e "DROP DATABASE IF EXISTS giasu; CREATE DATABASE giasu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p giasu < sql.sql
```

**Railway (Production):**
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
mysql -h ballast.proxy.rlwy.net -u root -p railway < railway.sql
```

**phpMyAdmin:**
1. Chọn database `giasu` hoặc `railway`
2. Tab **Import** → Chọn file `sql.sql` hoặc `railway.sql`
3. Bấm **Go**

---

#### 🟢 Cách 2: Chỉ thêm cột LanSua (Giữ data cũ)

Copy SQL này vào **phpMyAdmin** hoặc **MySQL Workbench**:

```sql
-- Thêm cột LanSua
ALTER TABLE DanhGia 
ADD COLUMN LanSua INT NOT NULL DEFAULT 0 
COMMENT 'Số lần sửa đánh giá (0=chưa sửa, 1=đã sửa 1 lần)';

-- Cập nhật data cũ
UPDATE DanhGia SET LanSua = 0;

-- Kiểm tra
SELECT DanhGiaID, DiemSo, NgayDanhGia, LanSua FROM DanhGia;
```

Hoặc chạy từ terminal:
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
mysql -u root -p giasu -e "ALTER TABLE DanhGia ADD COLUMN LanSua INT NOT NULL DEFAULT 0; UPDATE DanhGia SET LanSua = 0;"
```

---

## 🧹 Sau khi import xong:

### 1. Clear Laravel cache:
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 2. Kiểm tra database:
```sql
DESCRIBE DanhGia;
-- Phải thấy cột LanSua INT NOT NULL DEFAULT 0

SELECT * FROM DanhGia;
-- Tất cả record phải có LanSua = 0
```

---

## 🧪 Test ngay trên app:

### Test 1: Đánh giá lần đầu
1. Mở chi tiết gia sư
2. Bấm "Đánh giá"
3. Nhập điểm + nhận xét → Gửi
4. ✅ Thành công, LanSua = 0

### Test 2: Sửa lần 1 (Cho phép)
1. Bấm "Đánh giá" lần 2
2. ✅ Hiện **dialog CAM** cảnh báo "Chỉ sửa được 1 lần"
3. Bấm "Tiếp tục" → Sửa → Gửi
4. ✅ Thành công, LanSua = 1

### Test 3: Sửa lần 2 (Chặn)
1. Bấm "Đánh giá" lần 3
2. ✅ Hiện **dialog ĐỎ** chặn "Đã sửa rồi, không sửa nữa"
3. ✅ Không mở form, không cho sửa

---

## ❌ Nếu gặp lỗi:

### Lỗi: "Unknown column 'LanSua'"
→ Chưa chạy migration, quay lại **Cách 2** ở trên

### Lỗi: "Column 'LanSua' already exists"
→ Cột đã tồn tại, bỏ qua lỗi này

### Lỗi: HTTP 500
→ Chạy lại:
```cmd
php artisan cache:clear
php artisan config:clear
```

---

## 📚 Chi tiết đầy đủ:

Xem file: `README_DANH_GIA.md` (trong thư mục BE_GiaSu)

---

## 🎯 Tóm tắt:

1. **Import database** (Cách 1 hoặc Cách 2)
2. **Clear cache** Laravel
3. **Test** trên app (3 test cases)
4. **Done!** 🎉

✅ Hệ thống đánh giá với quy tắc **"Chỉ sửa 1 lần duy nhất"** hoạt động!
