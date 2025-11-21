<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LopHocYeuCau extends Model
{
    protected $table = 'LopHocYeuCau';
    protected $primaryKey = 'LopYeuCauID';
    public $timestamps = false;

    protected $fillable = [
        'NguoiHocID','GiaSuID','HinhThuc','HocPhi','ThoiLuong','TrangThai','SoLuong','MoTa',
        'MonID','KhoiLopID','DoiTuongID',
        'SoBuoiTuan',
        'LichHocMongMuon','NgayTao','TrangThaiThanhToan'
    ];

    protected $casts = [
        'NgayTao' => 'datetime',
        'HocPhi' => 'double',
        'SoLuong' => 'integer',
    ];

    public function nguoiHoc()
    {
        return $this->belongsTo(NguoiHoc::class, 'NguoiHocID', 'NguoiHocID');
    }

    public function giaSu()
    {
        return $this->belongsTo(GiaSu::class, 'GiaSuID', 'GiaSuID');
    }

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'MonID', 'MonID');
    }

    public function khoiLop()
    {
        return $this->belongsTo(KhoiLop::class, 'KhoiLopID', 'KhoiLopID');
    }

    public function doiTuong()
    {
        return $this->belongsTo(DoiTuong::class, 'DoiTuongID', 'DoiTuongID');
    }

    public function thoiGianDay()
    {
        return $this->belongsTo(ThoiGianDay::class, 'ThoiGianDayID', 'ThoiGianDayID');
    }

    public function lichHocs()
    {
        return $this->hasMany(LichHoc::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function yeuCauNhanLops()
    {
        return $this->hasMany(YeuCauNhanLop::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function giaoDiches()
    {
        return $this->hasMany(GiaoDich::class, 'LopYeuCauID', 'LopYeuCauID');
    }
}
