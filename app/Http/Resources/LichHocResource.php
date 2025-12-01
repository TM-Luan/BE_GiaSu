<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LichHocResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'LichHocID' => $this->LichHocID,
            'LopYeuCauID' => $this->LopYeuCauID,
            'ThoiGianBatDau' => $this->ThoiGianBatDau,
            'ThoiGianKetThuc' => $this->ThoiGianKetThuc,
            'NgayHoc' => $this->NgayHoc,
            'TrangThai' => $this->TrangThai,
            'DuongDan' => $this->DuongDan,
            'IsLapLai' => $this->IsLapLai,
            'NgayTao' => optional($this->NgayTao)->format('d-m-Y H:i'),
            
            'Lop' => $this->whenLoaded('lop', function() {
                if (!$this->lop) {
                    return null;
                }
                
                // Lấy thông tin từ các relationship đã load
                $tenNguoiHoc = ($this->lop->relationLoaded('nguoiHoc') && $this->lop->nguoiHoc)
                    ? $this->lop->nguoiHoc->HoTen
                    : 'Chưa rõ';
                
                // --- THÊM PHẦN NÀY ĐỂ LẤY ĐỊA CHỈ TỪ NGƯỜI HỌC ---
                $diaChi = ($this->lop->relationLoaded('nguoiHoc') && $this->lop->nguoiHoc)
                    ? $this->lop->nguoiHoc->DiaChi
                    : null;
                // -------------------------------------------------
                
                $tenMon = ($this->lop->relationLoaded('monHoc') && $this->lop->monHoc)
                    ? $this->lop->monHoc->TenMon
                    : null;

                $tenGiaSu = ($this->lop->relationLoaded('giaSu') && $this->lop->giaSu)
                    ? $this->lop->giaSu->HoTen
                    : null;
                
                $tenKhoiLop = ($this->lop->relationLoaded('khoiLop') && $this->lop->khoiLop)
                    ? $this->lop->khoiLop->BacHoc 
                    : null;

                // Trả về cấu trúc PHẲNG (flat) khớp với lophoc.dart
                return [
                    'LopYeuCauID' => $this->lop->LopYeuCauID,
                    'HocPhi' => $this->lop->HocPhi,
                    'ThoiLuong' => $this->lop->ThoiLuong,
                    'HinhThuc' => $this->lop->HinhThuc,
                    'TrangThai' => $this->lop->TrangThai,
                    'MoTa' => $this->lop->MoTa,
                    
                    // Các trường hiển thị
                    'TenNguoiHoc' => $tenNguoiHoc,
                    'TenMon' => $tenMon,
                    'TenGiaSu' => $tenGiaSu,
                    'TenKhoiLop' => $tenKhoiLop,
                    
                    // --- QUAN TRỌNG: TRẢ VỀ ĐỊA CHỈ ---
                    'DiaChi' => $diaChi, 
                    // ----------------------------------

                    // Các ID
                    'MonID' => $this->lop->MonID,
                    'KhoiLopID' => $this->lop->KhoiLopID,
                    'DoiTuongID' => $this->lop->DoiTuongID,
                    'ThoiGianDayID' => $this->lop->ThoiGianDayID,
                ];
            }),
        ];
    }
}