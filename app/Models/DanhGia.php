<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danhgia';
    protected $primaryKey = 'DanhGiaID';
    public $timestamps = false;

    protected $fillable = [
        'LopYeuCauID','TaiKhoanID','DiemSo','BinhLuan','NgayDanhGia'
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
