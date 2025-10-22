<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguoiHoc extends Model
{
    use HasFactory;

    protected $table = 'NguoiHoc';
    protected $primaryKey = 'NguoiHocID';
    public $timestamps = false; 

    protected $fillable = [
        'TaiKhoanID', 'HoTen', 'NgaySinh', 'GioiTinh', 'DiaChi',
       'AnhDaiDien'
    ];

    // Quan hệ
    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID','TaiKhoanID');
    }
}

