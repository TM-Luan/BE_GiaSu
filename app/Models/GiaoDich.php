<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiaoDich extends Model
{
    protected $table = 'GiaoDich';
    protected $primaryKey = 'GiaoDichID';
    public $timestamps = false;

    protected $fillable = [
        'LopYeuCauID','TaiKhoanID','SoTien','ThoiGian','TrangThai','GhiChu','LoaiGiaoDich','MaGiaoDich'
    ];

    protected $dates = ['ThoiGian'];

    protected $casts = [
        'ThoiGian' => 'datetime',
    ];

    public function lop()
    {
        return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID', 'TaiKhoanID');
    }
}
