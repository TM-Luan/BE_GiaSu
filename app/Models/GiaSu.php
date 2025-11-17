<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaSu extends Model
{
    use HasFactory;

    protected $table = 'GiaSu';
    protected $primaryKey = 'GiaSuID';
    public $timestamps = false; 

    protected $fillable = [
        'TaiKhoanID', 'HoTen', 'DiaChi', 'GioiTinh', 'NgaySinh',
        'AnhCCCD_MatTruoc', 'AnhCCCD_MatSau',
        'BangCap', 'AnhBangCap', 'TruongDaoTao', 'ChuyenNganh',
        'ThanhTich', 'KinhNghiem', 'AnhDaiDien',
        'TrangThai' // <<< ĐÃ THÊM
    ];

    // Quan hệ
    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID','TaiKhoanID');
    }
    
    // ... (Các quan hệ khác giữ nguyên) ...
    public function yeuCauNhanLop()
    {
        return $this->hasMany(YeuCauNhanLop::class, 'GiaSuID', 'GiaSuID');
    }
    
    public function lopHocYeuCau()
    {
        return $this->hasMany(LopHocYeuCau::class, 'GiaSuID', 'GiaSuID');
    }
    
    public function danhGia()
    {
        // Lấy đánh giá qua các lớp mà gia sư đã dạy
        return $this->hasManyThrough(
            DanhGia::class,
            LopHocYeuCau::class,
            'GiaSuID', // Foreign key on LopHocYeuCau
            'LopYeuCauID', // Foreign key on DanhGia
            'GiaSuID', // Local key on GiaSu
            'LopYeuCauID' // Local key on LopHocYeuCau
        );
    }
    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;

        return $query->where(function($q) use ($keyword) {
            $q->where('HoTen', 'LIKE', "%{$keyword}%")
            ->orWhere('ChuyenNganh', 'LIKE', "%{$keyword}%") // Tìm theo môn
            ->orWhere('TruongDaoTao', 'LIKE', "%{$keyword}%") // Tìm theo trường
            ->orWhere('DiaChi', 'LIKE', "%{$keyword}%");      // Tìm theo khu vực
        });
    }
}