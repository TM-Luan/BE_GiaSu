<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoiTuong extends Model
{
    use HasFactory;

    // Tuân thủ quy ước đặt tên bảng giống như NguoiHoc.php và GiaSu.php
    protected $table = 'doituong'; 
    
    protected $primaryKey = 'DoiTuongID';
    
    // Bảng 'doituong' không có cột created_at/updated_at
    public $timestamps = false; 

    protected $fillable = [
        'TenDoiTuong'
    ];

    /**
     * Lấy tất cả các lớp học yêu cầu thuộc về đối tượng này.
     * (Dựa trên khóa ngoại 'DoiTuongID' trong bảng 'lophocyeucau')
     */
    public function lopHocYeuCau()
    {
        return $this->hasMany(LopHocYeuCau::class, 'DoiTuongID', 'DoiTuongID');
    }
}
