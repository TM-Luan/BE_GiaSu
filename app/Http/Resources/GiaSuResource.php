<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GiaSuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'GiaSuID' => $this->GiaSuID,
            'HoTen' => $this->HoTen,
            'DiaChi' => $this->DiaChi,
            'GioiTinh' => $this->GioiTinh,
            'NgaySinh' => $this->NgaySinh,
            'BangCap' => $this->BangCap,
            'KinhNghiem' => $this->KinhNghiem,
            'AnhDaiDien' => $this->AnhDaiDien,
            'TaiKhoan' => [
                'TaiKhoanID' => $this->taiKhoan->TaiKhoanID,
                'Email' => $this->taiKhoan->Email,
                'SoDienThoai' => $this->taiKhoan->SoDienThoai,
            ]
        ];
    }
}
