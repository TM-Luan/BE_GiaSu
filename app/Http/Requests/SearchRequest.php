<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Common search parameters
            'keyword' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string|in:name,price,experience,created_at,duration,students',
            'sort_order' => 'nullable|string|in:asc,desc',
            
            // Price filters
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            
            // Category filters
            'subject_id' => 'nullable|integer|exists:MonHoc,MonID',
            'grade_id' => 'nullable|integer|exists:KhoiLop,KhoiLopID',
            'target_id' => 'nullable|integer|exists:DoiTuong,DoiTuongID',
            'time_id' => 'nullable|integer|exists:ThoiGianDay,ThoiGianDayID',
            
            // Tutor specific filters
            'gender' => 'nullable|string|in:Nam,Nữ,Khác',
            'education_level' => 'nullable|string',
            'experience_level' => 'nullable|string',
            'min_experience' => 'nullable|integer|min:0',
            'max_experience' => 'nullable|integer|min:0',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'max_rating' => 'nullable|numeric|min:0|max:5',
            
            // Class specific filters
            'form' => 'nullable|string|in:Online,Offline',
            'status' => 'nullable|string|in:TimGiaSu,ChoDuyet,DangHoc,HoanThanh',
            'location' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'keyword.max' => 'Từ khóa tìm kiếm không được quá 255 ký tự.',
            'min_price.numeric' => 'Giá tối thiểu phải là số.',
            'max_price.numeric' => 'Giá tối đa phải là số.',
            'subject_id.exists' => 'Môn học không tồn tại.',
            'grade_id.exists' => 'Khối lớp không tồn tại.',
            'target_id.exists' => 'Đối tượng không tồn tại.',
            'time_id.exists' => 'Thời gian dạy không tồn tại.',
            'gender.in' => 'Giới tính phải là Nam, Nữ hoặc Khác.',
            'experience_level.in' => 'Cấp độ kinh nghiệm không hợp lệ.',
            'min_experience.integer' => 'Kinh nghiệm tối thiểu phải là số.',
            'max_experience.integer' => 'Kinh nghiệm tối đa phải là số.',
            'min_rating.numeric' => 'Đánh giá tối thiểu phải là số.',
            'max_rating.numeric' => 'Đánh giá tối đa phải là số.',
            'min_rating.max' => 'Đánh giá tối thiểu không được vượt quá 5.',
            'max_rating.max' => 'Đánh giá tối đa không được vượt quá 5.',
            'form.in' => 'Hình thức dạy không hợp lệ.',
            'status.in' => 'Trạng thái lớp không hợp lệ.',
            'location.max' => 'Địa chỉ không được quá 255 ký tự.',
            'per_page.max' => 'Số lượng kết quả tối đa là 100.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set default values
        $this->merge([
            'per_page' => $this->per_page ?? 20,
            'sort_order' => $this->sort_order ?? 'desc',
            'page' => $this->page ?? 1,
        ]);
    }
}