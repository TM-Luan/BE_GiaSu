<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThoiGianDay extends Model
{
    protected $table = 'thoigianday';
    protected $primaryKey = 'ThoiGianDayID';
    public $timestamps = false;

    protected $fillable = ['SoBuoi','BuoiHoc','ThoiLuong'];

    public function lopYeuCau()
    {
        return $this->hasMany(LopHocYeuCau::class, 'ThoiGianDayID', 'ThoiGianDayID');
    }
}
