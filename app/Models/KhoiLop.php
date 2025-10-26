<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhoiLop extends Model
{
    protected $table = 'khoilop';
    protected $primaryKey = 'KhoiLopID';
    public $timestamps = false;

    protected $fillable = ['BacHoc'];

    public function lopYeuCau()
    {
        return $this->hasMany(LopHocYeuCau::class, 'KhoiLopID', 'KhoiLopID');
    }
}
