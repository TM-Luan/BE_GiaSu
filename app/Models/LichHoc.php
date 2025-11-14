<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHoc extends Model
{
    protected $table = 'LichHoc';
    protected $primaryKey = 'LichHocID';
    public $timestamps = false;

    protected $fillable = [
        'LopYeuCauID',
        'ThoiGianBatDau',
        'ThoiGianKetThuc',
        'NgayHoc',
        'TrangThai',
        'DuongDan',
        'NgayTao',
        'LichHocGocID',
        'IsLapLai'
    ];

    protected $casts = [
        'NgayTao' => 'datetime',
        'NgayHoc' => 'datetime',
    ];

    public function lopHocYeuCau()
    {
        return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
    }

    public function lichHocCon()
    {
        return $this->hasMany(LichHoc::class, 'LichHocGocID', 'LichHocID');
    }

    public function lichHocGoc()
    {
        return $this->belongsTo(LichHoc::class, 'LichHocGocID', 'LichHocID');
    }

    public function lop()
{
    return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
}
    public function scopeTheoThang($query, $thang, $nam)
    {
        return $query->whereYear('NgayHoc', $nam)
            ->whereMonth('NgayHoc', $thang);
    }

    public function scopeTheoLop($query, $lopYeuCauId)
    {
        return $query->where('LopYeuCauID', $lopYeuCauId);
    }
}