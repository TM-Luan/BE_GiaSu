# ✅ CHECKLIST - Import Database

## 📋 Trước khi import:

- [x] ✅ File `sql.sql` đã có cột `LanSua`
- [x] ✅ File `railway.sql` đã có cột `LanSua`
- [x] ✅ INSERT statements đã có giá trị `,0` cho LanSua
- [x] ✅ Backend `DanhGiaController.php` đã có logic chặn
- [x] ✅ Frontend `tutor_detail_page.dart` đã có dialog cảnh báo

---

## 🚀 Bạn cần làm:

### [ ] 1. Backup database cũ (Khuyến nghị)
```cmd
# Local
mysqldump -u root -p giasu > backup_giasu_08_11_2025.sql

# Railway
mysqldump -h ballast.proxy.rlwy.net -u root -p railway > backup_railway_08_11_2025.sql
```

### [ ] 2. Import database mới

**Chọn 1 trong 2:**

#### Cách A: Import toàn bộ ✅ (Khuyến nghị cho dev)
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu

# Local
mysql -u root -p giasu < sql.sql

# Railway
mysql -h ballast.proxy.rlwy.net -u root -p railway < railway.sql
```

#### Cách B: Chỉ thêm cột (Giữ data cũ)
```sql
ALTER TABLE DanhGia ADD COLUMN LanSua INT NOT NULL DEFAULT 0;
UPDATE DanhGia SET LanSua = 0;
SELECT * FROM DanhGia;
```

### [ ] 3. Kiểm tra database
```sql
-- Xem cấu trúc
DESCRIBE DanhGia;

-- Kết quả mong đợi:
-- LanSua | int | NO | | 0

-- Xem dữ liệu
SELECT DanhGiaID, DiemSo, NgayDanhGia, LanSua FROM DanhGia;

-- Kết quả mong đợi:
-- Tất cả record có LanSua = 0
```

### [ ] 4. Clear Laravel cache
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### [ ] 5. Restart backend server
```cmd
# Nhấn Ctrl+C để dừng
# Chạy lại:
php artisan serve
```

---

## 🧪 Test trên app:

### [ ] Test 1: Đánh giá lần đầu
1. Mở chi tiết gia sư
2. Bấm "Đánh giá"
3. Nhập điểm + nhận xét → Gửi
4. **Mong đợi:**
   - ✅ Toast: "Đánh giá thành công"
   - ✅ Database: LanSua = 0

### [ ] Test 2: Sửa lần 1 (Cho phép)
1. Bấm "Đánh giá" lần 2
2. **Mong đợi:**
   - ✅ Hiện dialog CAM (màu cam)
   - ✅ Nội dung: "Bạn chỉ có thể sửa 1 lần duy nhất"
3. Bấm "Tiếp tục" → Sửa → Gửi
4. **Mong đợi:**
   - ✅ Toast: "Cập nhật thành công"
   - ✅ Database: LanSua = 1

### [ ] Test 3: Sửa lần 2 (Chặn)
1. Bấm "Đánh giá" lần 3
2. **Mong đợi:**
   - ✅ Hiện dialog ĐỎ (màu đỏ)
   - ✅ Nội dung: "Bạn đã sửa rồi, không thể sửa nữa"
   - ✅ Không có nút "Tiếp tục"
   - ✅ Chỉ có nút "Đóng"
   - ✅ Database: LanSua vẫn = 1 (không thay đổi)

### [ ] Test 4: Kiểm tra backend (Postman - Optional)
```
POST http://localhost:8000/api/danh-gia/tao
Headers: Authorization: Bearer <token>
Body: {
  "lop_yeu_cau_id": 1,
  "tai_khoan_id": 4,  // Đã có LanSua = 1
  "diem_so": 5.0,
  "binh_luan": "Test"
}

Mong đợi:
Status: 403 Forbidden
Response: {
  "success": false,
  "message": "Bạn đã chỉnh sửa đánh giá này rồi..."
}
```

---

## ✅ Khi tất cả test PASS:

- [x] ✅ Hệ thống đánh giá hoạt động đúng
- [x] ✅ Quy tắc "Chỉ sửa 1 lần" được thực thi
- [x] ✅ Backend chặn ở API level
- [x] ✅ Frontend chặn ở UI level
- [x] ✅ Database đồng bộ (local + production)

---

## 🎊 HOÀN THÀNH!

Commit code lên Git:
```cmd
git add .
git commit -m "feat: Thêm hệ thống đánh giá gia sư - Chỉ sửa 1 lần duy nhất"
git push origin main
```

---

## 📞 Troubleshooting:

### ❌ Lỗi: "Unknown column 'LanSua'"
**Nguyên nhân:** Chưa import database
**Giải pháp:** Quay lại Bước 2

### ❌ Lỗi: HTTP 500 khi đánh giá
**Nguyên nhân:** Laravel cache cũ
**Giải pháp:** Quay lại Bước 4 (Clear cache)

### ❌ Dialog không hiện màu đúng
**Nguyên nhân:** Frontend chưa rebuild
**Giải pháp:** 
```cmd
flutter clean
flutter pub get
flutter run
```

### ❌ Vẫn sửa được nhiều lần
**Nguyên nhân:** Logic backend chưa đúng
**Giải pháp:** Kiểm tra file `DanhGiaController.php` line ~80

---

**Chúc bạn import và test thành công!** 🚀
