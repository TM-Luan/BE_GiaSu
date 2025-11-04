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
        'NgayTao'
    ];

    protected $dates = ['NgayTao', 'NgayHoc'];

    public function lopHocYeuCau()
    {
        return $this->belongsTo(LopHocYeuCau::class, 'LopYeuCauID', 'LopYeuCauID');
    }
}
