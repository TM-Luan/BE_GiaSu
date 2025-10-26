<?php

namespace App\Http\Resources;
use App\Models\NguoiHoc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NguoiHocResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'NguoiHocID' => $this->NguoiHocID,
            'HoTen' => $this->HoTen,
            'DiaChi' => $this->DiaChi,
            'GioiTinh' => $this->GioiTinh,
            'NgaySinh' => $this->NgaySinh,    
            'AnhDaiDien' => $this->AnhDaiDien,
            'TaiKhoan' => [
                'TaiKhoanID' => $this->taiKhoan->TaiKhoanID,
                'Email' => $this->taiKhoan->Email,
                'SoDienThoai' => $this->taiKhoan->SoDienThoai,
            ]
        ];
    }
}
