<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'TaiKhoan';
    protected $primaryKey = 'TaiKhoanID';
    public $timestamps = false; // schema dùng NgayTao default

    protected $fillable = [
        'Email', 'MatKhauHash', 'SoDienThoai', 'TrangThai','fcm_token'
    ];

    protected $hidden = [
        'MatKhauHash'
    ];

    // override default password attribute expected by Laravel
    public function getAuthPassword()
    {
        return $this->MatKhauHash;
    }

    public function giasu()
    {
        return $this->hasOne(GiaSu::class, 'TaiKhoanID', 'TaiKhoanID');
    }
    public function nguoihoc()
    {
        return $this->hasOne(NguoiHoc::class, 'TaiKhoanID', 'TaiKhoanID');
    }
    public function phanquyen()
    {
        return $this->hasOne(PhanQuyen::class, 'TaiKhoanID', 'TaiKhoanID');
    }
}