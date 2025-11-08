<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'DanhGia';
    protected $primaryKey = 'DanhGiaID';
    public $timestamps = false; // Tắt timestamps vì bảng chưa có

    protected $fillable = [
        'LopYeuCauID','TaiKhoanID','DiemSo','BinhLuan','NgayDanhGia','LanSua'
    ];

    protected $dates = ['NgayDanhGia'];

    public function lop()
    {
        return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID', 'TaiKhoanID');
    }
}
