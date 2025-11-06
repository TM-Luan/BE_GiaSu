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
        // Set to true to allow requests.
        // You can add logic here later, e.g., check if auth()->user()->isNguoiHoc()
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // Validation rules based on your sql.sql schema
        return [
            'NguoiHocID' => 'required|exists:NguoiHoc,NguoiHocID',
            'GiaSuID' => 'nullable|exists:GiaSu,GiaSuID',
            'HinhThuc' => 'required|string|max:100',
            'HocPhi' => 'required|numeric|min:0',
            'ThoiLuong' => 'required|string|max:50',
            'TrangThai' => [
                'nullable',
                Rule::in(['ChoDuyet', 'TimGiaSu', 'DangChonGiaSu', 'DangHoc', 'HoanThanh', 'Huy'])
            ],
            'SoLuong' => 'nullable|integer|min:1',
            'MoTa' => 'nullable|string',
            'MonID' => 'required|exists:MonHoc,MonID',
            'KhoiLopID' => 'required|exists:KhoiLop,KhoiLopID',
            'DoiTuongID' => 'required|exists:DoiTuong,DoiTuongID',
            'ThoiGianDayID' => 'required|exists:ThoiGianDay,ThoiGianDayID',
        ];
    }
    
}