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
            
            // Thông tin lớp học liên quan
            'Lop' => $this->whenLoaded('lop', function() {
                if (!$this->lop) {
                    return null;
                }
                
                return [
                    'LopYeuCauID' => $this->lop->LopYeuCauID,
                    'HocPhi' => $this->lop->HocPhi,
                    'ThoiLuong' => $this->lop->ThoiLuong,
                    'HinhThuc' => $this->lop->HinhThuc,
                    'TrangThai' => $this->lop->TrangThai,
                    'MoTa' => $this->lop->MoTa,
                    
                    // Thông tin học viên
                    'NguoiHoc' => $this->whenLoaded('lop.nguoiHoc', function() {
                        if (!$this->lop->nguoiHoc) {
                            return null;
                        }
                        return [
                            'NguoiHocID' => $this->lop->nguoiHoc->NguoiHocID,
                            'HoTen' => $this->lop->nguoiHoc->HoTen,
                            'Email' => $this->lop->nguoiHoc->taiKhoan->Email ?? null,
                            'SoDienThoai' => $this->lop->nguoiHoc->taiKhoan->SoDienThoai ?? null,
                            'DiaChi' => $this->lop->nguoiHoc->DiaChi,
                        ];
                    }),
                    
                    // Thông tin gia sư
                    'GiaSu' => $this->whenLoaded('lop.giaSu', function() {
                        if (!$this->lop->giaSu) {
                            return null;
                        }
                        return [
                            'GiaSuID' => $this->lop->giaSu->GiaSuID,
                            'HoTen' => $this->lop->giaSu->HoTen,
                            'Email' => $this->lop->giaSu->taiKhoan->Email ?? null,
                            'SoDienThoai' => $this->lop->giaSu->taiKhoan->SoDienThoai ?? null,
                            'BangCap' => $this->lop->giaSu->BangCap,
                            'KinhNghiem' => $this->lop->giaSu->KinhNghiem,
                        ];
                    }),
                    
                    // Thông tin môn học
                    'MonHoc' => $this->whenLoaded('lop.monHoc', function() {
                        if (!$this->lop->monHoc) {
                            return null;
                        }
                        return [
                            'MonID' => $this->lop->monHoc->MonID,
                            'TenMon' => $this->lop->monHoc->TenMon,
                        ];
                    }),
                    
                    // Thông tin khối lớp
                    'KhoiLop' => $this->whenLoaded('lop.khoiLop', function() {
                        if (!$this->lop->khoiLop) {
                            return null;
                        }
                        return [
                            'KhoiLopID' => $this->lop->khoiLop->KhoiLopID,
                            'BacHoc' => $this->lop->khoiLop->BacHoc,
                        ];
                    }),
                    
                    // Thông tin đối tượng
                    'DoiTuong' => $this->whenLoaded('lop.doiTuong', function() {
                        if (!$this->lop->doiTuong) {
                            return null;
                        }
                        return [
                            'DoiTuongID' => $this->lop->doiTuong->DoiTuongID,
                            'TenDoiTuong' => $this->lop->doiTuong->TenDoiTuong,
                        ];
                    }),
                    
                    // Thông tin thời gian dạy
                    'ThoiGianDay' => $this->whenLoaded('lop.thoiGianDay', function() {
                        if (!$this->lop->thoiGianDay) {
                            return null;
                        }
                        return [
                            'ThoiGianDayID' => $this->lop->thoiGianDay->ThoiGianDayID,
                            'SoBuoi' => $this->lop->thoiGianDay->SoBuoi,
                            'BuoiHoc' => $this->lop->thoiGianDay->BuoiHoc,
                            'ThoiLuong' => $this->lop->thoiGianDay->ThoiLuong,
                        ];
                    }),
                ];
            }),
        ];
    }
}
