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
        // Đảm bảo Controller (cả index() và show()) đã eager load:
        // ['nguoiHoc', 'monHoc', 'khoiLop', 'giaSu', 'doiTuong', 'thoiGianDay']

        return [
            'MaLop' => $this->LopYeuCauID,
            
            // --- CÁC DÒNG DƯỚI ĐÂY ĐÃ ĐƯỢC THÊM KIỂM TRA NULL (ví dụ: $this->monHoc ? ...) ---

            'TieuDeLop' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : '') 
                           . ' ' 
                           . $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : ''),
            
            'TenNguoiHoc' => $this->whenLoaded('nguoiHoc', $this->nguoiHoc ? $this->nguoiHoc->HoTen : null),
            
            'DiaChi' => $this->NguoiHoc ? $this->NguoiHoc->DiaChi : null,
            'HocPhi' => $this->HocPhi . ' vnd/Buoi',
            'TrangThai' => $this->TrangThai,
            'MoTaChiTiet' => $this->MoTa,
            
            'HinhThuc' => $this->HinhThuc,
            'ThoiLuong' => $this->ThoiLuong,
            'SoLuong' => $this->SoLuong,

            'DoiTuong' => $this->whenLoaded('doiTuong', $this->doiTuong ? $this->doiTuong->TenDoiTuong : null),
            'ThoiGianHoc' => $this->whenLoaded('thoiGianDay', 
                                $this->thoiGianDay ? $this->thoiGianDay->BuoiHoc . ' (' . $this->thoiGianDay->SoBuoi . ')' : null
                            ),
            
            'MonID' => $this->MonID,
            'KhoiLopID' => $this->KhoiLopID,
            'NgayTao' => $this->NgayTao->format('d-m-Y'), // Model đã cast nên an toàn

            'TenMon' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : null),
            'TenKhoiLop' => $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : null),
            
            // Đây là dòng gây lỗi 500
            'TenGiaSu' => $this->whenLoaded('giaSu', $this->giaSu ? $this->giaSu->HoTen : null), // Thêm ?
            
            'GiaSu' => new GiaSuResource($this->whenLoaded('giaSu')),
        ];
    }
}