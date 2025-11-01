<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class YeuCauNhanLopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $lop = $this->whenLoaded('lop');
        $monHoc = $lop ? $lop->monHoc : null;
        $khoiLop = $lop ? $lop->khoiLop : null;
        $nguoiHoc = $lop ? $lop->nguoiHoc : null;
        $giaSu = $this->whenLoaded('giaSu');
        $nguoiGui = $this->whenLoaded('nguoiGuiTaiKhoan');

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
            'NguoiGui' => $nguoiGui ? [
                'TaiKhoanID' => $nguoiGui->TaiKhoanID,
                'HoTen' => $nguoiGui->HoTen,
                'Email' => $nguoiGui->Email,
                'SoDienThoai' => $nguoiGui->SoDienThoai,
            ] : null,
            'GiaSu' => $giaSu ? [
                'GiaSuID' => $giaSu->GiaSuID,
                'HoTen' => $giaSu->HoTen,
                'AnhDaiDien' => $giaSu->AnhDaiDien,
            ] : null,
            'MaLop' => $lop ? $lop->LopYeuCauID : null,
            'TieuDeLop' => $monHoc && $khoiLop
                ? $monHoc->TenMon . ' ' . $khoiLop->BacHoc
                : ($monHoc ? $monHoc->TenMon : null),
            'TenNguoiHoc' => $nguoiHoc ? $nguoiHoc->HoTen : null,
            'HocPhi' => $lop ? $lop->HocPhi . ' vnd/Buoi' : null,
            'TrangThaiLop' => $lop ? $lop->TrangThai : null,
            'TenMon' => $monHoc ? $monHoc->TenMon : null,
            'TenKhoiLop' => $khoiLop ? $khoiLop->BacHoc : null,
            'HinhThuc' => $lop ? $lop->HinhThuc : null,
            'ThoiLuong' => $lop ? $lop->ThoiLuong : null,
        ];
    }
}
