# 📚 TÀI LIỆU HỆ THỐNG ĐÁNH GIÁ GIA SƯ

## 🎯 Mục đích: Cho phép học viên đánh giá gia sư - CHỈ SỬA 1 LẦN DUY NHẤT

---

## 📖 Hướng dẫn đọc tài liệu:

### 1. **CHECKLIST.md** ← 🚀 BẮT ĐẦU TỪ ĐÂY
   - Danh sách công việc cần làm từng bước
   - Hướng dẫn import database
   - Test cases chi tiết
   - **Đọc đầu tiên để biết phải làm gì**

### 2. **QUICK_START.md**
   - Hướng dẫn nhanh 5 phút
   - 2 cách import database
   - Commands cơ bản
   - **Đọc nếu muốn làm nhanh**

### 3. **README_DANH_GIA.md**
   - Tài liệu đầy đủ về hệ thống
   - Workflow chi tiết
   - Code examples (Backend + Frontend)
   - SQL debug queries
   - **Đọc để hiểu sâu về hệ thống**

### 4. **SUMMARY.md**
   - Tóm tắt công việc đã làm
   - So sánh trước/sau
   - Troubleshooting
   - **Đọc để review lại toàn bộ**

### 5. **INDEX.md** (File này)
   - Mục lục tổng hợp
   - Hướng dẫn đọc tài liệu

---

## 🗂️ Cấu trúc thư mục:

```
BE_GiaSu/
│
├── sql.sql                    ✅ Database local (cá nhân)
├── railway.sql                ✅ Database production (nhóm)
│
├── CHECKLIST.md               📋 Bắt đầu từ đây
├── QUICK_START.md             ⚡ Hướng dẫn nhanh
├── README_DANH_GIA.md         📚 Tài liệu đầy đủ
├── SUMMARY.md                 📊 Tóm tắt
├── INDEX.md                   📖 File này
│
├── app/
│   ├── Models/
│   │   └── DanhGia.php        ✅ Model (timestamps = false)
│   └── Http/Controllers/
│       └── DanhGiaController.php  ✅ Logic chặn (LanSua >= 1)
│
└── routes/
    └── api.php                ✅ 4 endpoints đánh giá
```

---

## 🎯 Luồng công việc khuyến nghị:

```
1. Đọc CHECKLIST.md
   ↓
2. Backup database cũ
   ↓
3. Import sql.sql hoặc railway.sql
   ↓
4. Clear Laravel cache
   ↓
5. Test 3 scenarios trên app
   ↓
6. ✅ HOÀN THÀNH
```

---

## 📊 Thống kê:

| File | Dòng | Mục đích |
|------|------|----------|
| CHECKLIST.md | ~150 | Hướng dẫn từng bước |
| QUICK_START.md | ~100 | Hướng dẫn nhanh |
| README_DANH_GIA.md | ~500 | Tài liệu đầy đủ |
| SUMMARY.md | ~150 | Tóm tắt |
| INDEX.md | ~80 | Mục lục |
| **TỔNG** | **~980 dòng** | **Documentation** |

---

## 🔑 Từ khóa chính:

- **LanSua**: Cột đếm số lần sửa (0 hoặc 1)
- **Dialog CAM**: Cảnh báo khi LanSua = 0
- **Dialog ĐỎ**: Chặn khi LanSua >= 1
- **HTTP 403**: Status code khi backend chặn
- **timestamps = false**: Không dùng created_at/updated_at

---

## 🚀 Quick Commands:

```bash
# Import local database
mysql -u root -p giasu < sql.sql

# Import railway database
mysql -h ballast.proxy.rlwy.net -u root -p railway < railway.sql

# Clear Laravel cache
php artisan cache:clear && php artisan config:clear

# Kiểm tra database
mysql -u root -p giasu -e "DESCRIBE DanhGia"

# Test backend API
curl -X POST http://localhost:8000/api/danh-gia/kiem-tra \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "lop_yeu_cau_id=1&tai_khoan_id=4"
```

---

## 📞 Liên hệ & Support:

- **Repository**: KhoaLuanTotNgiep_GiaSu_NguoiHoc
- **Branch**: main
- **Backend**: Laravel 10 + Sanctum
- **Frontend**: Flutter/Dart + BLoC
- **Database**: MySQL 8.0

---

## 🎊 Changelog:

### v1.0.0 - 08/11/2025
- ✅ Thêm cột `LanSua` vào bảng `DanhGia`
- ✅ Backend logic chặn sửa lần 2
- ✅ Frontend dialog cảnh báo 2 cấp
- ✅ Đồng bộ `sql.sql` và `railway.sql`
- ✅ Tài liệu đầy đủ 5 files

---

## 📚 Đọc theo vai trò:

### 👨‍💻 Developer (Đọc tất cả):
1. CHECKLIST.md
2. README_DANH_GIA.md
3. QUICK_START.md
4. SUMMARY.md

### 🧪 Tester (Test flow):
1. CHECKLIST.md → Phần "Test trên app"
2. README_DANH_GIA.md → Phần "Test Scenarios"

### 📋 Project Manager (Review):
1. SUMMARY.md
2. INDEX.md (file này)

### 🔧 DevOps (Deploy):
1. QUICK_START.md
2. sql.sql / railway.sql

---

**Chúc bạn triển khai thành công!** 🚀

---

**Cập nhật lần cuối:** 08/11/2025  
**Tác giả:** AI Assistant  
**Trạng thái:** ✅ HOÀN THÀNH
