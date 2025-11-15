<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanQuyen extends Model
{
    protected $table = 'PhanQuyen';
    public $incrementing = false; // Không có auto-increment primary key
    protected $primaryKey = null; // Composite key
    public $timestamps = false;

    protected $fillable = ['TaiKhoanID', 'VaiTroID'];

    public function taikhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID', 'TaiKhoanID');
    }

    public function vaitro()
    {
        return $this->belongsTo(VaiTro::class, 'VaiTroID', 'VaiTroID');
    }
}