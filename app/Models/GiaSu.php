<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaSu extends Model
{
    use HasFactory;

    protected $table = 'giasu';
    protected $primaryKey = 'GiaSuID';
    public $timestamps = false; 

    protected $fillable = [
        'TaiKhoanID', 'HoTen', 'DiaChi', 'GioiTinh', 'NgaySinh',
        'BangCap', 'KinhNghiem', 'AnhDaiDien'
    ];

    // Quan hệ
    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID','TaiKhoanID');
    }
    public function yeuCauNhanLop()
    {
        return $this->hasMany(YeuCauNhanLop::class, 'GiaSuID', 'GiaSuID');
    }
    public function DanhGia()
{
    return $this->hasOne(DanhGia::class, 'TaiKhoanID', 'TaiKhoanID');
}
}

