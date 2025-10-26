<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    protected $table = 'monhoc';
    protected $primaryKey = 'MonID';
    public $timestamps = false;

    protected $fillable = ['TenMon'];

    public function lopYeuCau()
    {
        return $this->hasMany(LopHocYeuCau::class, 'MonID', 'MonID');
    }
}
