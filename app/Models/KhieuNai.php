<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhieuNai extends Model
{
    protected $table = 'khieunai';
    protected $primaryKey = 'KhieuNaiID';
    public $timestamps = false;

    protected $fillable = [
        'TaiKhoanID','NoiDung','NgayTao','TrangThai','GiaiQuyet','PhanHoi','GiaoDichID','LopYeuCauID'
    ];

    protected $dates = ['NgayTao'];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'TaiKhoanID', 'TaiKhoanID');
    }

    public function giaoDich()
    {
        return $this->belongsTo(GiaoDich::class, 'GiaoDichID', 'GiaoDichID');
    }

    public function lop()
    {
        return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
    }
}
