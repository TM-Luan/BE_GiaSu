# Migration: Thêm Timestamps cho Bảng DanhGia

## 🎯 Mục Đích
Thêm 2 cột `created_at` và `updated_at` vào bảng `DanhGia` để tracking việc chỉnh sửa đánh giá.

## 📋 Chi Tiết

### Các cột được thêm:
- **created_at**: Thời điểm tạo đánh giá lần đầu
- **updated_at**: Thời điểm cập nhật đánh giá gần nhất

### Logic kiểm tra đã sửa:
```php
$daSua = !$danhGia->created_at->eq($danhGia->updated_at);
```

## 🚀 Cách Chạy Migration

### Bước 1: Backup Database
```sql
-- Tạo backup trước khi migration
BACKUP DATABASE [TenDatabase] 
TO DISK = 'D:\Backup\DanhGia_backup.bak';
```

### Bước 2: Chạy Migration Script
```bash
# Mở SQL Server Management Studio (SSMS)
# Mở file: BE_GiaSu/database/migrations/add_timestamps_to_danhgia.sql
# Thay [TenDatabase] bằng tên database thực tế
# Chạy script (F5)
```

### Bước 3: Verify
```sql
-- Kiểm tra cấu trúc bảng
EXEC sp_columns 'DanhGia';

-- Kiểm tra dữ liệu
SELECT TOP 10 
    DanhGiaID,
    created_at,
    updated_at,
    CASE 
        WHEN created_at = updated_at THEN 'Chưa sửa'
        ELSE 'Đã sửa'
    END AS Status
FROM DanhGia;
```

## ✅ Expected Results

### Trước Migration:
```
DanhGia
├── DanhGiaID
├── LopYeuCauID
├── TaiKhoanID
├── DiemSo
├── BinhLuan
└── NgayDanhGia
```

### Sau Migration:
```
DanhGia
├── DanhGiaID
├── LopYeuCauID
├── TaiKhoanID
├── DiemSo
├── BinhLuan
├── NgayDanhGia
├── created_at    ← MỚI
└── updated_at    ← MỚI
```

## 🔄 Rollback (Nếu Cần)

```sql
-- Xóa các cột đã thêm
ALTER TABLE DanhGia DROP COLUMN created_at;
ALTER TABLE DanhGia DROP COLUMN updated_at;

-- Restore từ backup
RESTORE DATABASE [TenDatabase] 
FROM DISK = 'D:\Backup\DanhGia_backup.bak'
WITH REPLACE;
```

## 🧪 Testing

### Test 1: Đánh giá mới
```sql
-- Sau khi tạo đánh giá mới
SELECT 
    DanhGiaID,
    created_at,
    updated_at,
    created_at = updated_at AS IsNew
FROM DanhGia 
WHERE DanhGiaID = [ID_mới_nhất]
-- Expected: IsNew = 1 (True)
```

### Test 2: Sửa đánh giá
```sql
-- Sau khi sửa đánh giá
SELECT 
    DanhGiaID,
    created_at,
    updated_at,
    created_at != updated_at AS IsEdited
FROM DanhGia 
WHERE DanhGiaID = [ID_đã_sửa]
-- Expected: IsEdited = 1 (True)
```

### Test 3: Sửa lần 2 (phải bị chặn)
```php
// API sẽ trả về error 403
{
  "success": false,
  "message": "Bạn đã chỉnh sửa đánh giá này rồi. Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất."
}
```

## 📝 Notes

1. **Dữ liệu cũ**: Các đánh giá đã tồn tại sẽ có `created_at = updated_at = NgayDanhGia`
2. **Laravel Timestamps**: Model đã được cập nhật để sử dụng `public $timestamps = true`
3. **Nullable**: Các cột mới là nullable để tránh lỗi với dữ liệu cũ
4. **Default Value**: GETDATE() được set làm default khi insert

## 🎉 Done!

Sau khi chạy migration thành công:
- ✅ Backend có thể track việc sửa đánh giá
- ✅ API kiểm tra và chặn sửa lần 2
- ✅ Frontend hiển thị dialog cảnh báo đúng
- ✅ UX rõ ràng: "Chỉ được sửa 1 LẦN DUY NHẤT"
