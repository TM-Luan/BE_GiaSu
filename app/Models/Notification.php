<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\TaiKhoan;
class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // Tên bảng trong CSDL

    protected $fillable = [
        'user_id',      // ID của người nhận thông báo (TaiKhoanID)
        'title',        // Tiêu đề
        'message',      // Nội dung
        'type',         // Loại thông báo (request_class, accept_class, v.v.)
        'related_id',   // ID liên quan (ví dụ: ID lớp học hoặc ID yêu cầu)
        'is_read',      // Trạng thái đã đọc
    ];

    // Relationship để lấy thông tin người nhận (nếu cần)
    public function user()
    {
        // Sửa User::class thành TaiKhoan::class
        return $this->belongsTo(TaiKhoan::class, 'user_id', 'TaiKhoanID');
    }
}