<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    protected $table = 'VaiTro';
    protected $primaryKey = 'VaiTroID';
    public $timestamps = false;

    protected $fillable = ['TenVaiTro', 'MoTa'];

    public function taiKhoan()
    {
        return $this->belongsToMany(TaiKhoan::class, 'PhanQuyen', 'VaiTroID', 'TaiKhoanID');
    }
}
