<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class YeuCauNhanLopResource extends JsonResource
{
    public function toArray($request)
    {
        $lop = $this->whenLoaded('lop');
        $monHoc = optional($lop)->monHoc;
        $khoiLop = optional($lop)->khoiLop;
        $nguoiHoc = optional($lop)->nguoiHoc;
        $giaSu = optional($this->whenLoaded('giaSu'));
        $nguoiGui = optional($this->whenLoaded('nguoiGuiTaiKhoan'));

        return [
            'YeuCauID' => $this->YeuCauID,
            'LopYeuCauID' => $this->LopYeuCauID,
            'GiaSuID' => $this->GiaSuID,
            'NguoiGuiTaiKhoanID' => $this->NguoiGuiTaiKhoanID,
            'VaiTroNguoiGui' => $this->VaiTroNguoiGui,
            'TrangThai' => $this->TrangThai,
            'GhiChu' => $this->GhiChu,
            'NgayTao' => optional($this->NgayTao)->format('d-m-Y H:i'),
            'NgayCapNhat' => optional($this->NgayCapNhat)->format('d-m-Y H:i'),

            'NguoiGui' => $nguoiGui->TaiKhoanID ? [
                'TaiKhoanID' => $nguoiGui->TaiKhoanID,
                'HoTen' => $nguoiGui->HoTen,
                'Email' => $nguoiGui->Email,
                'SoDienThoai' => $nguoiGui->SoDienThoai,
            ] : null,

            'GiaSu' => $giaSu->GiaSuID ? [
                'GiaSuID' => $giaSu->GiaSuID,
                'HoTen' => $giaSu->HoTen,
                'AnhDaiDien' => $giaSu->AnhDaiDien,
            ] : null,

            'MaLop' => optional($lop)->LopYeuCauID,
            'TieuDeLop' => optional($monHoc)->TenMon
                ? trim(optional($monHoc)->TenMon . ' ' . (optional($khoiLop)->BacHoc ?? ''))
                : null,
            'TenNguoiHoc' => optional($nguoiHoc)->HoTen,
            'HocPhi' => optional($lop)->HocPhi ? optional($lop)->HocPhi . ' vnd/Buoi' : null,
            'TrangThaiLop' => optional($lop)->TrangThai,
            'TenMon' => optional($monHoc)->TenMon,
            'TenKhoiLop' => optional($khoiLop)->BacHoc,
            'HinhThuc' => optional($lop)->HinhThuc,
            'ThoiLuong' => optional($lop)->ThoiLuong,
        ];
    }
}
