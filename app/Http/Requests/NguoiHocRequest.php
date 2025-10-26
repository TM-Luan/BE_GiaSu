<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NguoiHocRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'TaiKhoanID' => 'required|exists:TaiKhoan,TaiKhoanID',
            'HoTen' => 'required|string|max:150',
            'NgaySinh' => 'nullable|date',
            'GioiTinh' => 'nullable|string|max:10',
            'DiaChi' => 'nullable|string|max:255',
            'AnhDaiDien' => 'nullable|string|max:255',
        ];
    }
}
