<?php

namespace App\Http\Resources;
use App\Models\DanhGia;
use Illuminate\Http\Resources\Json\JsonResource;

class GiaSuResource extends JsonResource
{  
    public function toArray($request)
    {
        // Logic tính điểm đánh giá giữ nguyên
        $danhGiaQuery = DanhGia::whereHas('lop', function($query) {
            $query->where('GiaSuID', $this->GiaSuID);
        });
        $diemTrungBinh = $danhGiaQuery->avg('DiemSo');
        $tongSoDanhGia = $danhGiaQuery->count();

        return [
            'GiaSuID' => $this->GiaSuID,
            'HoTen' => $this->HoTen,
            'DiaChi' => $this->DiaChi,
            'GioiTinh' => $this->GioiTinh,
            'NgaySinh' => $this->NgaySinh,
            'AnhCCCD_MatTruoc' => $this->AnhCCCD_MatTruoc,
            'AnhCCCD_MatSau' => $this->AnhCCCD_MatSau,
            'BangCap' => $this->BangCap,
            
            // [CẬP NHẬT] Thêm trường TenMon
            'TenMon' => $this->monHoc ? $this->monHoc->TenMon : null,
            
            'AnhBangCap' => $this->AnhBangCap,
            'TruongDaoTao' => $this->TruongDaoTao,
            'ChuyenNganh' => $this->ChuyenNganh,
            'ThanhTich' => $this->ThanhTich,
            'KinhNghiem' => $this->KinhNghiem,
            'AnhDaiDien' => $this->AnhDaiDien,
            'DiemSo' => $diemTrungBinh ? round($diemTrungBinh, 1) : 0.0,
            'TongSoDanhGia' => $tongSoDanhGia,
            'TaiKhoan' => [
                'TaiKhoanID' => $this->taiKhoan->TaiKhoanID,
                'Email' => $this->taiKhoan->Email,
                'SoDienThoai' => $this->taiKhoan->SoDienThoai,
            ]
        ];
    }
}