<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhieuNai extends Model
{
    protected $table = 'KhieuNai';
    protected $primaryKey = 'KhieuNaiID';
    public $timestamps = false;

    /**
     * Cập nhật $fillable để khớp với sql.sql
     * Đã loại bỏ 'GhiChu' và 'NgayXuLy'
     */
    protected $fillable = [
        'TaiKhoanID',
        'NoiDung',
        'NgayTao',
        'TrangThai',
        'GiaiQuyet',
        'PhanHoi',
        'GiaoDichID',
        'LopYeuCauID'
    ];

    /**
     * Cập nhật $dates
     */
    protected $dates = ['NgayTao'];

    /**
     * Cập nhật $casts
     */
    protected $casts = [
        'NgayTao' => 'datetime',
    ];

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