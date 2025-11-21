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
        // ĐỒNG BỘ VỚI MOBILE: Kiểm tra thanh toán từ cột TrangThaiThanhToan
        // Fallback: Nếu cột chưa có, kiểm tra qua bảng GiaoDich
        $isPaid = false;
        
        if (isset($this->TrangThaiThanhToan)) {
            // Cách mới: Dùng cột TrangThaiThanhToan
            $isPaid = $this->TrangThaiThanhToan === 'DaThanhToan';
        } else {
            // Cách cũ: Kiểm tra qua bảng GiaoDich (backward compatibility)
            $isPaid = $this->giaoDiches->contains(function ($giaoDich) {
                return $giaoDich->TrangThai === 'Thành công';
            });
        }

        return [
            'MaLop' => $this->LopYeuCauID,
            // ... giữ nguyên các trường cũ ...
            'TieuDeLop' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : '') 
                           . ' ' 
                           . $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : ''),
            
            'TenNguoiHoc' => $this->whenLoaded('nguoiHoc', $this->nguoiHoc ? $this->nguoiHoc->HoTen : null),
            
            'SoDienThoai' => $this->whenLoaded('nguoiHoc', function() {
                return ($this->nguoiHoc && $this->nguoiHoc->taiKhoan) 
                    ? $this->nguoiHoc->taiKhoan->SoDienThoai 
                    : null;
            }),

            'DiaChi' => $this->NguoiHoc ? $this->NguoiHoc->DiaChi : null,
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
            'NgayTao' => $this->NgayTao->format('d-m-Y'),
            'TenMon' => $this->whenLoaded('monHoc', $this->monHoc ? $this->monHoc->TenMon : null),
            'TenKhoiLop' => $this->whenLoaded('khoiLop', $this->khoiLop ? $this->khoiLop->BacHoc : null),
            'TenGiaSu' => $this->whenLoaded('giaSu', $this->giaSu ? $this->giaSu->HoTen : null), 
            'GiaSu' => new GiaSuResource($this->whenLoaded('giaSu')),
            
            // === QUAN TRỌNG: Thêm trường này để Frontend nhận biết ===
            'TrangThaiThanhToan' => $isPaid ? 'DaThanhToan' : 'ChuaThanhToan',
            // ========================================================
        ];
    }
}