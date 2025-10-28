<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LopHocYeuCauResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // We format the data to match your UI (image_b7f5a6.png)
        return [
            'MaLop' => $this->LopYeuCauID,
            
            // Combine MonHoc and KhoiLop to create the title
            'TieuDeLop' => $this->whenLoaded('monHoc', $this->monHoc->TenMon) . ' ' . $this->whenLoaded('khoiLop', $this->khoiLop->BacHoc),
            
            // Load NguoiHoc relationship
            'TenNguoiHoc' => $this->whenLoaded('nguoiHoc', $this->nguoiHoc->HoTen),
            'DiaChi' => $this->whenLoaded('nguoiHoc', $this->nguoiHoc->DiaChi), // Address seems to come from NguoiHoc

            'HocPhi' => $this->HocPhi . ' vnd/Buoi',
            'TrangThai' => $this->TrangThai,
            'MoTaChiTiet' => $this->MoTa,
            
            // Load GiaSu if assigned (nullable)
            'GiaSu' => new GiaSuResource($this->whenLoaded('giaSu')),

            // You can add other related data here
            // 'DoiTuong' => $this.whenLoaded('doiTuong', $this->doiTuong->TenDoiTuong),
            // 'ThoiGianDay' => $this.whenLoaded('thoiGianDay'),
        ];
    }
}
