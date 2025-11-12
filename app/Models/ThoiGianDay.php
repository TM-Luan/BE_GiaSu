<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThoiGianDay extends Model
{
    protected $table = 'ThoiGianDay';
    protected $primaryKey = 'ThoiGianDayID';
    public $timestamps = false;

    protected $fillable = ['SoBuoi','BuoiHoc'];

    public function lopYeuCau()
    {
        return $this->hasMany(LopHocYeuCau::class, 'ThoiGianDayID', 'ThoiGianDayID');
    }
}
