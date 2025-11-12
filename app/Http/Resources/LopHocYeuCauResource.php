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
        // Controller đã xóa 'thoiGianDay'
        // ['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong']

        return [
            'MaLop' => $this->LopYeuCauID,
            
            'TieuDeLop' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : '') 
                           . ' ' 
                           . $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : ''),
            
            'TenNguoiHoc' => $this->whenLoaded('nguoiHoc', $this->nguoiHoc ? $this->nguoiHoc->HoTen : null),
            
            'DiaChi' => $this->NguoiHoc ? $this->NguoiHoc->DiaChi : null,
            'HocPhi' => $this->HocPhi,
            'TrangThai' => $this->TrangThai,
            'MoTaChiTiet' => $this->MoTa, // Khớp với Flutter (MoTa)
            
            'HinhThuc' => $this->HinhThuc,
            'ThoiLuong' => $this->ThoiLuong,
            'SoLuong' => $this->SoLuong,

            'DoiTuong' => $this->whenLoaded('doiTuong', $this->doiTuong ? $this->doiTuong->TenDoiTuong : null),
            
            // SỬA: Xóa 'ThoiGianHoc' và thêm 2 trường mới
            'SoBuoiTuan' => $this->SoBuoiTuan,
            'LichHocMongMuon' => $this->LichHocMongMuon,
            
            'MonID' => $this->MonID,
            'KhoiLopID' => $this->KhoiLopID,
            'DoiTuongID' => $this->DoiTuongID,
            'NgayTao' => $this->NgayTao->format('d-m-Y'),

            'TenMon' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : null),
            'TenKhoiLop' => $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : null),
            
            'TenGiaSu' => $this->whenLoaded('giaSu', $this->giaSu ? $this->giaSu->HoTen : null), 
            
            'GiaSu' => new GiaSuResource($this->whenLoaded('giaSu')),
        ];
    }
}