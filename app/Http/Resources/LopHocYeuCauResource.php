<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GiaSuResource; // <--- Thêm dòng này để tránh lỗi class not found

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
        // XỬ LÝ LOGIC TRẠNG THÁI THANH TOÁN
        $isPaid = false;
        
        if (isset($this->TrangThaiThanhToan)) {
            // Ưu tiên 1: Kiểm tra cột TrangThaiThanhToan trong bảng
            $isPaid = $this->TrangThaiThanhToan === 'DaThanhToan';
        } else {
            // Ưu tiên 2 (Fallback): Kiểm tra bảng GiaoDich (cho dữ liệu cũ)
            // Lưu ý: Model LopHocYeuCau cần có function giaoDiches() { return $this->hasMany(GiaoDich::class, ...); }
            if ($this->relationLoaded('giaoDiches')) {
                 $isPaid = $this->giaoDiches->contains(function ($giaoDich) {
                    return $giaoDich->TrangThai === 'Thành công';
                });
            }
        }

        return [
            'MaLop' => $this->LopYeuCauID,
            'TieuDeLop' => $this->TieuDeLop ?? ($this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : '') . ' ' . $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : '')),
            
            'TenNguoiHoc' => $this->whenLoaded('nguoiHoc', $this->nguoiHoc ? $this->nguoiHoc->HoTen : null),
            
            'SoDienThoai' => $this->whenLoaded('nguoiHoc', function() {
                return ($this->nguoiHoc && $this->nguoiHoc->taiKhoan) 
                    ? $this->nguoiHoc->taiKhoan->SoDienThoai 
                    : null;
            }),

            'DiaChi' => $this->nguoiHoc ? $this->nguoiHoc->DiaChi : null,
            'HocPhi' => $this->HocPhi,
            'TrangThai' => $this->TrangThai,
            'MoTaChiTiet' => $this->MoTa,
            'HinhThuc' => $this->HinhThuc,
            'ThoiLuong' => $this->ThoiLuong,
            'SoLuong' => $this->SoLuong,
            
            'DoiTuong' => $this->whenLoaded('doiTuong', $this->doiTuong ? $this->doiTuong->TenDoiTuong : null),
            'SoBuoiTuan' => $this->SoBuoiTuan,
            'LichHocMongMuon' => $this->LichHocMongMuon,
            
            'MonID' => $this->MonID,
            'KhoiLopID' => $this->KhoiLopID,
            'DoiTuongID' => $this->DoiTuongID,
            'NgayTao' => $this->NgayTao ? $this->NgayTao->format('d-m-Y') : null,
            
            'TenMon' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : null),
            'TenKhoiLop' => $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : null),
            
            'TenGiaSu' => $this->whenLoaded('giaSu', $this->giaSu ? $this->giaSu->HoTen : null), 
            'GiaSu' => new GiaSuResource($this->whenLoaded('giaSu')),
            
            // === TRẢ VỀ TRẠNG THÁI THANH TOÁN CHO CLIENT ===
            'TrangThaiThanhToan' => $isPaid ? 'DaThanhToan' : 'ChuaThanhToan',
            // ===============================================
        ];
    }
}