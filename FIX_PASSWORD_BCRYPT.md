# Hướng dẫn sửa lỗi "This password does not use the Bcrypt algorithm"

## Nguyên nhân

Lỗi này xảy ra khi mật khẩu trong database không được mã hóa bằng thuật toán Bcrypt. Có thể mật khẩu được lưu dạng plain text (ví dụ: `123456`) thay vì dạng hash (ví dụ: `$2y$10$...`).

## Giải pháp đã áp dụng

### 1. Cập nhật AuthController.php

Đã sửa hàm `login()` để:
- Kiểm tra xem mật khẩu đã được hash chưa
- Nếu chưa hash, so sánh trực tiếp và tự động hash lại
- Lưu mật khẩu đã hash vào database

```php
// Kiểm tra nếu mật khẩu đã được hash
if (Hash::needsRehash($tk->MatKhauHash) === false && Hash::check($request->MatKhau, $tk->MatKhauHash)) {
    $isPasswordValid = true;
} 
// Nếu mật khẩu chưa hash, so sánh trực tiếp và hash lại
elseif ($tk->MatKhauHash === $request->MatKhau) {
    $isPasswordValid = true;
    // Tự động hash lại mật khẩu
    $tk->MatKhauHash = Hash::make($request->MatKhau);
    $tk->save();
}
```

### 2. Cập nhật Admin\Auth\AdminLoginController.php

Đã sửa hàm `login()` tương tự để xử lý cả mật khẩu plain text và đã hash.

### 3. Tạo Artisan Command để hash tất cả mật khẩu

Đã tạo command `password:hash-plain` để hash tất cả mật khẩu plain text còn lại trong database.

## Cách sử dụng

### Cách 1: Tự động hash khi đăng nhập (Khuyến nghị)

Chỉ cần đăng nhập bình thường. Hệ thống sẽ tự động:
1. Phát hiện mật khẩu chưa hash
2. So sánh với mật khẩu nhập vào
3. Hash lại và lưu vào database

**Ví dụ:**
- Database có: `MatKhauHash = "123456"` (plain text)
- Bạn đăng nhập với mật khẩu: `123456`
- Sau khi đăng nhập thành công, database sẽ có: `MatKhauHash = "$2y$10$..."`

### Cách 2: Hash tất cả mật khẩu bằng Command (Nhanh hơn)

Chạy command sau trong terminal:

```bash
cd BE_GiaSu
php artisan password:hash-plain
```

Command này sẽ:
- Tìm tất cả tài khoản có mật khẩu plain text
- Hash lại toàn bộ mật khẩu
- Hiển thị tiến trình

**Output mẫu:**
```
Đang kiểm tra và hash các mật khẩu plain text...
✓ Đã hash mật khẩu cho: admin@example.com
✓ Đã hash mật khẩu cho: user1@example.com
✓ Đã hash mật khẩu cho: user2@example.com
✓ Hoàn thành! Đã hash 3 mật khẩu.
```

### Cách 3: Hash thủ công bằng SQL (Nếu biết mật khẩu gốc)

Nếu bạn biết mật khẩu gốc của admin, có thể hash thủ công:

```bash
php artisan tinker
```

Trong tinker, chạy:

```php
$tk = \App\Models\TaiKhoan::where('Email', 'admin@example.com')->first();
$tk->MatKhauHash = \Illuminate\Support\Facades\Hash::make('123456');
$tk->save();
```

## Test sau khi sửa

### Test API Login (Postman)

**Endpoint:** `POST http://127.0.0.1:8000/api/login`

**Body (JSON):**
```json
{
  "Email": "admin@example.com",
  "MatKhau": "123456"
}
```

**Response thành công:**
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "data": {
    "TaiKhoanID": 1,
    "Email": "admin@example.com",
    "HoTen": "Admin",
    "VaiTro": 1
  },
  "token": "..."
}
```

### Test Admin Web Login

1. Truy cập: `http://127.0.0.1:8000/admin/login`
2. Nhập email và mật khẩu
3. Nếu thành công, sẽ redirect về dashboard

## Lưu ý quan trọng

1. **Backup database trước khi chạy command hash:**
   ```bash
   mysqldump -u username -p database_name > backup.sql
   ```

2. **Mật khẩu mặc định:** Nếu không nhớ mật khẩu, có thể reset về `123456` hoặc `password`:
   ```bash
   php artisan tinker
   $tk = \App\Models\TaiKhoan::find(1);
   $tk->MatKhauHash = \Hash::make('123456');
   $tk->save();
   ```

3. **Kiểm tra định dạng hash:** Mật khẩu Bcrypt luôn bắt đầu bằng `$2y$` hoặc `$2a$`

## Ngăn ngừa lỗi trong tương lai

Luôn sử dụng `Hash::make()` khi tạo/cập nhật mật khẩu:

```php
// ✅ ĐÚNG
$taiKhoan->MatKhauHash = Hash::make($request->MatKhau);

// ❌ SAI - Không lưu plain text
$taiKhoan->MatKhauHash = $request->MatKhau;
```

## Checklist

- [x] Sửa AuthController::login()
- [x] Sửa AdminLoginController::login()
- [x] Tạo command hash mật khẩu
- [ ] Chạy command hash tất cả mật khẩu: `php artisan password:hash-plain`
- [ ] Test đăng nhập API
- [ ] Test đăng nhập Admin web
- [ ] Backup database
