<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GiaSuRequest extends FormRequest
{
    public function authorize()
    {
        return true; // tùy bạn thêm auth sau
    }

    public function rules()
    {
        return [
            'TaiKhoanID' => 'required|exists:TaiKhoan,TaiKhoanID',
            'HoTen' => 'required|string|max:150',
            'DiaChi' => 'nullable|string|max:255',
            'GioiTinh' => 'nullable|string|max:10',
            'NgaySinh' => 'nullable|date',
            'BangCap' => 'nullable|string|max:255',
            'KinhNghiem' => 'nullable|string|max:255',
            'AnhDaiDien' => 'nullable|string|max:255',
        ];
    }
}

