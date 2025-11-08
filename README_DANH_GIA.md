# 📊 HỆ THỐNG ĐÁNH GIÁ GIA SƯ - Chỉ sửa 1 lần duy nhất

## 🎯 Mô tả tính năng

Hệ thống cho phép học viên đánh giá gia sư sau khi đăng ký và học. **Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất.**

### Quy tắc nghiệp vụ:
1. ✅ **Đánh giá lần đầu**: Học viên tạo đánh giá mới (LanSua = 0)
2. ✅ **Sửa lần 1**: Học viên có thể sửa đánh giá (LanSua tăng lên 1)
3. 🚫 **Cấm sửa lần 2**: Sau khi sửa 1 lần, hệ thống chặn hoàn toàn (backend + frontend)

---

## 📁 Cấu trúc Database

### Bảng `DanhGia` (đã cập nhật)

```sql
CREATE TABLE `DanhGia` (
  `DanhGiaID` int NOT NULL AUTO_INCREMENT,
  `LopYeuCauID` int NOT NULL,
  `TaiKhoanID` int NOT NULL,
  `DiemSo` double NOT NULL,
  `BinhLuan` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayDanhGia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LanSua` int NOT NULL DEFAULT '0' COMMENT 'Đếm số lần sửa (0=chưa, 1=đã sửa 1 lần)',
  PRIMARY KEY (`DanhGiaID`),
  KEY `LopYeuCauID` (`LopYeuCauID`),
  KEY `TaiKhoanID` (`TaiKhoanID`),
  CONSTRAINT `DanhGia_ibfk_1` FOREIGN KEY (`LopYeuCauID`) REFERENCES `LopHocYeuCau` (`LopYeuCauID`) ON DELETE CASCADE,
  CONSTRAINT `DanhGia_ibfk_2` FOREIGN KEY (`TaiKhoanID`) REFERENCES `TaiKhoan` (`TaiKhoanID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Cột quan trọng: `LanSua`
- **Kiểu dữ liệu**: `INT NOT NULL DEFAULT 0`
- **Mục đích**: Đếm số lần học viên đã chỉnh sửa đánh giá
- **Giá trị**:
  - `0`: Chưa sửa lần nào → **Cho phép sửa 1 lần**
  - `1`: Đã sửa 1 lần → **🚫 Chặn hoàn toàn**
  - `>1`: Lỗi logic (không bao giờ xảy ra nếu backend đúng)

---

## 🔄 Workflow đánh giá

```
┌─────────────────────────────────────────────────────────────────┐
│ BƯỚC 1: Học viên bấm "Đánh giá" trên chi tiết gia sư          │
└─────────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│ Frontend gọi API: kiemTraDaDanhGia()                           │
│ • Kiểm tra học viên đã từng đánh giá gia sư này chưa?         │
│ • Trả về: { da_danh_gia: bool, da_sua: bool }                 │
└─────────────────────────────────────────────────────────────────┘
                            ↓
              ┌─────────────┴─────────────┐
              │                           │
        (Chưa đánh giá)              (Đã đánh giá)
              │                           │
              ↓                           ↓
   ┌──────────────────────┐    ┌──────────────────────┐
   │ Cho phép tạo mới     │    │ Kiểm tra LanSua      │
   │ LanSua = 0           │    └──────────────────────┘
   └──────────────────────┘               │
                                     ┌─────┴─────┐
                                     │           │
                               (LanSua = 0)  (LanSua >= 1)
                                     │           │
                                     ↓           ↓
                          ┌─────────────────┐  ┌─────────────────┐
                          │ Dialog CAM      │  │ Dialog ĐỎ       │
                          │ "Bạn chỉ sửa    │  │ "Đã sửa rồi,    │
                          │  được 1 lần"    │  │  không sửa nữa" │
                          │ → Cho phép sửa  │  │ → Chặn hoàn toàn│
                          └─────────────────┘  └─────────────────┘
                                     │
                                     ↓
                          ┌─────────────────┐
                          │ Gọi taoDanhGia()│
                          │ LanSua → 1      │
                          └─────────────────┘
```

---

## 🎨 Frontend (Flutter/Dart)

### 1. Model: `danhgia.dart`

```dart
class KiemTraDanhGiaResponse {
  final bool daDanhGia;
  final bool daSua;    // ← Dựa vào LanSua >= 1
  
  KiemTraDanhGiaResponse({
    required this.daDanhGia,
    required this.daSua,
  });
}
```

### 2. UI Flow: `tutor_detail_page.dart`

**Dialog CAM (Cảnh báo - LanSua = 0)**
```dart
// Hiện khi học viên chưa sửa lần nào
showDialog(
  context: context,
  builder: (_) => AlertDialog(
    backgroundColor: Colors.orange.shade50,
    icon: Icon(Icons.warning_amber_rounded, color: Colors.orange, size: 48),
    title: Text('⚠️ Lưu ý quan trọng'),
    content: Text('Bạn chỉ có thể sửa đánh giá này 1 lần duy nhất.\n\n'
                  'Sau khi sửa lần này, bạn sẽ KHÔNG THỂ thay đổi nữa.'),
    actions: [
      // Nút Hủy + Nút Tiếp tục
    ],
  ),
);
```

**Dialog ĐỎ (Chặn - LanSua >= 1)**
```dart
// Hiện khi học viên đã sửa 1 lần rồi
showDialog(
  context: context,
  builder: (_) => AlertDialog(
    backgroundColor: Colors.red.shade50,
    icon: Icon(Icons.block, color: Colors.red, size: 48),
    title: Text('🚫 Không thể chỉnh sửa'),
    content: Text('Bạn đã chỉnh sửa đánh giá này rồi.\n\n'
                  'Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất.'),
    actions: [
      // Chỉ có nút Đóng
    ],
  ),
);
```

---

## ⚙️ Backend (Laravel PHP)

### 1. Model: `DanhGia.php`

```php
class DanhGia extends Model
{
    // TẮT timestamps vì không dùng created_at/updated_at
    public $timestamps = false;
    
    protected $table = 'DanhGia';
    protected $primaryKey = 'DanhGiaID';
    
    protected $fillable = [
        'LopYeuCauID',
        'TaiKhoanID',
        'DiemSo',
        'BinhLuan',
        'NgayDanhGia',
        'LanSua',  // ← Cột quan trọng
    ];
}
```

### 2. Controller: `DanhGiaController.php`

#### API 1: Kiểm tra đã đánh giá

```php
public function kiemTraDaDanhGia(Request $request)
{
    $danhGia = DanhGia::where('LopYeuCauID', $request->lop_yeu_cau_id)
                      ->where('TaiKhoanID', $request->tai_khoan_id)
                      ->first();
    
    if (!$danhGia) {
        return response()->json([
            'da_danh_gia' => false,
            'da_sua' => false,
        ]);
    }
    
    $lanSua = $danhGia->LanSua ?? 0;
    $daSua = ($lanSua >= 1);  // ← Logic kiểm tra
    
    return response()->json([
        'da_danh_gia' => true,
        'da_sua' => $daSua,
        'danh_gia_id' => $danhGia->DanhGiaID,
    ]);
}
```

#### API 2: Tạo/Cập nhật đánh giá (Chặn nếu LanSua >= 1)

```php
public function taoDanhGia(Request $request)
{
    $validator = Validator::make($request->all(), [
        'lop_yeu_cau_id' => 'required|exists:LopHocYeuCau,LopYeuCauID',
        'tai_khoan_id' => 'required|exists:TaiKhoan,TaiKhoanID',
        'diem_so' => 'required|numeric|min:1|max:5',
        'binh_luan' => 'nullable|string|max:500',
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 400);
    }
    
    // Kiểm tra đã có đánh giá chưa
    $danhGiaExists = DanhGia::where('LopYeuCauID', $request->lop_yeu_cau_id)
                            ->where('TaiKhoanID', $request->tai_khoan_id)
                            ->first();
    
    if ($danhGiaExists) {
        // ✅ LOGIC CHẶN: Kiểm tra LanSua
        $lanSua = $danhGiaExists->LanSua ?? 0;
        
        if ($lanSua >= 1) {
            // 🚫 ĐÃ SỬA 1 LẦN RỒI - CHẶN HOÀN TOÀN
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã chỉnh sửa đánh giá này rồi. Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất.',
            ], 403);  // HTTP 403 Forbidden
        }
        
        // ✅ LanSua = 0: Cho phép sửa lần đầu
        $danhGiaExists->update([
            'DiemSo' => $request->diem_so,
            'BinhLuan' => $request->binh_luan,
            'NgayDanhGia' => now(),
            'LanSua' => $lanSua + 1,  // Tăng lên 1
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đánh giá thành công (Lần sửa: 1/1)',
            'data' => $danhGiaExists,
        ], 200);
    }
    
    // Tạo mới (LanSua = 0)
    $danhGia = DanhGia::create([
        'LopYeuCauID' => $request->lop_yeu_cau_id,
        'TaiKhoanID' => $request->tai_khoan_id,
        'DiemSo' => $request->diem_so,
        'BinhLuan' => $request->binh_luan,
        'NgayDanhGia' => now(),
        'LanSua' => 0,  // Chưa sửa lần nào
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Tạo đánh giá thành công',
        'data' => $danhGia,
    ], 201);
}
```

### 3. Routes: `api.php`

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('danh-gia')->group(function () {
        Route::post('/tao', [DanhGiaController::class, 'taoDanhGia']);
        Route::post('/kiem-tra', [DanhGiaController::class, 'kiemTraDaDanhGia']);
        Route::get('/theo-gia-su/{id}', [DanhGiaController::class, 'layDanhGiaTheoGiaSu']);
        Route::get('/theo-nguoi-hoc/{id}', [DanhGiaController::class, 'layDanhGiaTheoNguoiHoc']);
    });
});
```

---

## 📦 Cài đặt Database

### Cách 1: Import toàn bộ (Khuyến nghị cho dev)

**Local MySQL (`sql.sql`)**
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
mysql -u root -p -e "DROP DATABASE IF EXISTS giasu; CREATE DATABASE giasu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p giasu < sql.sql
```

**Railway Production (`railway.sql`)**
```cmd
cd d:\DoAnTotNghiep\KhoaLuanTotNgiep_GiaSu_NguoiHoc\BE_GiaSu
mysql -h ballast.proxy.rlwy.net -u root -p railway < railway.sql
```

### Cách 2: Chỉ thêm cột LanSua (Giữ dữ liệu cũ)

Chạy SQL này trong phpMyAdmin:

```sql
-- Thêm cột LanSua vào bảng DanhGia
ALTER TABLE DanhGia ADD COLUMN LanSua INT NOT NULL DEFAULT 0 COMMENT 'Số lần sửa (0=chưa, 1=đã sửa 1 lần)';

-- Cập nhật tất cả đánh giá cũ: LanSua = 0
UPDATE DanhGia SET LanSua = 0;

-- Kiểm tra kết quả
SELECT DanhGiaID, DiemSo, NgayDanhGia, LanSua FROM DanhGia;
```

### Sau khi import/update:

```cmd
cd BE_GiaSu
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🧪 Test Scenarios

### Test Case 1: Đánh giá lần đầu ✅
**Bước:**
1. Học viên chưa từng đánh giá gia sư này
2. Bấm nút "Đánh giá"
3. Nhập điểm + nhận xét → Gửi

**Kết quả mong đợi:**
- ✅ Tạo record mới: `LanSua = 0`
- ✅ Toast hiển thị: "Đánh giá thành công"
- ✅ Danh sách gia sư tự động refresh

### Test Case 2: Sửa lần 1 (Cho phép) ✅
**Bước:**
1. Học viên đã đánh giá rồi (`LanSua = 0`)
2. Bấm "Đánh giá" lần 2
3. Hiện **dialog CAM** cảnh báo
4. Bấm "Tiếp tục" → Sửa điểm/nhận xét → Gửi

**Kết quả mong đợi:**
- ✅ Cập nhật record: `LanSua = 0 → 1`
- ✅ Toast hiển thị: "Cập nhật đánh giá thành công (Lần sửa: 1/1)"
- ✅ Danh sách gia sư tự động refresh

### Test Case 3: Sửa lần 2 (Chặn) 🚫
**Bước:**
1. Học viên đã sửa 1 lần rồi (`LanSua = 1`)
2. Bấm "Đánh giá" lần 3

**Kết quả mong đợi:**
- ✅ Hiện **dialog ĐỎ** chặn: "Bạn đã sửa rồi, không thể sửa nữa"
- ✅ Không mở form nhập liệu
- ✅ Backend trả 403 nếu bypass frontend

### Test Case 4: Bypass frontend (Postman) 🔒
**Bước:**
1. Dùng Postman gọi API `POST /api/danh-gia/tao`
2. Body: `lop_yeu_cau_id`, `tai_khoan_id` (đã có LanSua = 1)

**Kết quả mong đợi:**
```json
{
  "success": false,
  "message": "Bạn đã chỉnh sửa đánh giá này rồi. Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất."
}
```
- ✅ HTTP Status: **403 Forbidden**

---

## 🔍 SQL Debug Queries

### Kiểm tra cấu trúc bảng
```sql
DESCRIBE DanhGia;
```

### Xem tất cả đánh giá với trạng thái
```sql
SELECT 
    DanhGiaID,
    LopYeuCauID,
    TaiKhoanID,
    DiemSo,
    BinhLuan,
    NgayDanhGia,
    LanSua,
    CASE 
        WHEN LanSua = 0 THEN '✅ Chưa sửa (có thể sửa 1 lần)'
        WHEN LanSua = 1 THEN '🚫 Đã sửa 1 lần (không sửa nữa)'
        ELSE '⚠️ Lỗi: Quá số lần cho phép'
    END AS TrangThai
FROM DanhGia
ORDER BY NgayDanhGia DESC;
```

### Thống kê số lượng
```sql
SELECT 
    COUNT(*) AS TongSoDanhGia,
    SUM(CASE WHEN LanSua = 0 THEN 1 ELSE 0 END) AS ChuaSua,
    SUM(CASE WHEN LanSua = 1 THEN 1 ELSE 0 END) AS DaSua1Lan,
    SUM(CASE WHEN LanSua > 1 THEN 1 ELSE 0 END) AS LoiLogic
FROM DanhGia;
```

### Reset đánh giá cụ thể (Dev only)
```sql
-- Cho phép học viên sửa lại (dev test)
UPDATE DanhGia 
SET LanSua = 0 
WHERE DanhGiaID = 4;
```

---

## 📝 Checklist Hoàn Thành

### Backend ✅
- [x] Model `DanhGia.php`: Tắt timestamps, thêm fillable `LanSua`
- [x] Controller `DanhGiaController.php`: Logic kiểm tra `LanSua >= 1`
- [x] Routes `api.php`: 4 endpoints đánh giá
- [x] Validation: Kiểm tra eligibility (đã học lớp chưa)

### Frontend ✅
- [x] Model `danhgia.dart`: `KiemTraDanhGiaResponse` với `daSua`
- [x] Repository `danhgia_repository.dart`: 4 methods API
- [x] BLoC `danhgia_bloc.dart`: State management
- [x] UI `tutor_detail_page.dart`:
  - [x] Dialog CAM (LanSua = 0)
  - [x] Dialog ĐỎ (LanSua >= 1)
  - [x] Form đánh giá
  - [x] Auto-reload danh sách gia sư

### Database ✅
- [x] Bảng `DanhGia`: Thêm cột `LanSua INT NOT NULL DEFAULT 0`
- [x] File `sql.sql`: Cập nhật CREATE TABLE + INSERT
- [x] File `railway.sql`: Cập nhật CREATE TABLE + INSERT

### Documentation ✅
- [x] README đầy đủ (file này)
- [x] Workflow diagram
- [x] Test scenarios
- [x] Debug queries

---

## 🎊 Kết luận

Hệ thống đánh giá đã được triển khai hoàn chỉnh với quy tắc **"Chỉ sửa 1 lần duy nhất"**:

1. ✅ Backend chặn ở API level (`LanSua >= 1` → HTTP 403)
2. ✅ Frontend chặn ở UI level (Dialog đỏ + disable form)
3. ✅ Database sử dụng cột `LanSua` thay vì timestamps
4. ✅ Đồng bộ giữa `sql.sql` (local) và `railway.sql` (production)

**Import database rồi test ngay!** 🚀
