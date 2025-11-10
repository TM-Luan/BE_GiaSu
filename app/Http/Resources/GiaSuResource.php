<?php

namespace App\Http\Resources;
use App\Models\GiaSu;
use App\Models\DanhGia;
use App\Models\LopHocYeuCau;
use Illuminate\Http\Resources\Json\JsonResource;

class GiaSuResource extends JsonResource
{  
    public function toArray($request)
    {
        // Tính điểm đánh giá trung bình từ tất cả các lớp của gia sư
        $diemTrungBinh = DanhGia::whereHas('lop', function($query) {
            $query->where('GiaSuID', $this->GiaSuID);
        })->avg('DiemSo');

        // Đếm tổng số đánh giá
        $tongSoDanhGia = DanhGia::whereHas('lop', function($query) {
            $query->where('GiaSuID', $this->GiaSuID);
        })->count();

        return [
            'GiaSuID' => $this->GiaSuID,
            'HoTen' => $this->HoTen,
            'DiaChi' => $this->DiaChi,
            'GioiTinh' => $this->GioiTinh,
            'NgaySinh' => $this->NgaySinh,
            'AnhCCCD_MatTruoc' => $this->AnhCCCD_MatTruoc,
            'AnhCCCD_MatSau' => $this->AnhCCCD_MatSau,
            'BangCap' => $this->BangCap,
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

