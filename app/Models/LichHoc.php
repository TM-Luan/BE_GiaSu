<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHoc extends Model
{
    protected $table = 'lichhoc';
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

    protected $dates = ['NgayTao', 'NgayHoc'];

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

    public function scopeLaGoc($query)
    {
        return $query->whereColumn('LichHocID', 'LichHocGocID')
                    ->orWhereNull('LichHocGocID');
    }

    public function getLaBuoiGocAttribute()
    {
        return $this->LichHocID == $this->LichHocGocID || $this->LichHocGocID === null;
    }
}