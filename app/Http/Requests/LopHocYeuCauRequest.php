<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LopHocYeuCauRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'NguoiHocID' => 'required|exists:NguoiHoc,NguoiHocID',
            'GiaSuID' => 'nullable|exists:GiaSu,GiaSuID',
            'HinhThuc' => [
                'required',
                Rule::in(['Online', 'Offline'])
            ],
            'HocPhi' => 'required|numeric|min:0',
            'ThoiLuong' => 'required|integer|min:1', 
            'TrangThai' => [
                'nullable',
                Rule::in(['TimGiaSu', 'DangHoc', 'HoanThanh', 'Huy'])
            ],
            'SoLuong' => 'nullable|integer|min:1',
            'MoTa' => 'nullable|string',
            'MonID' => 'required|exists:MonHoc,MonID',
            'KhoiLopID' => 'required|exists:KhoiLop,KhoiLopID',
            'DoiTuongID' => 'required|exists:DoiTuong,DoiTuongID',
            
            // SỬA: Thay thế ThoiGianDayID bằng 2 trường mới
            'SoBuoiTuan' => 'nullable|integer|min:1',
            'LichHocMongMuon' => 'nullable|string|max:255',
        ];
    }
}