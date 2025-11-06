<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauNhanLop extends Model
{
    protected $table = 'YeuCauNhanLop';
    protected $primaryKey = 'YeuCauID';
    public $timestamps = false; 

    protected $fillable = [
        'LopYeuCauID','GiaSuID','NguoiGuiTaiKhoanID','VaiTroNguoiGui',
        'TrangThai','GhiChu','NgayTao','NgayCapNhat'
    ];

    protected $dates = ['NgayTao','NgayCapNhat'];

    public function lop()
    {
        return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function giaSu()
    {
        return $this->belongsTo(GiaSu::class, 'GiaSuID', 'GiaSuID');
    }

    public function nguoiGuiTaiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'NguoiGuiTaiKhoanID', 'TaiKhoanID');
    }
}
