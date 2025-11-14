@extends('layouts.web')

@section('title', 'Gửi khiếu nại')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('nguoihoc.lophoc.show', $lopHoc->LopYeuCauID) }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại chi tiết lớp
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Gửi khiếu nại</h1>
        <p class="text-gray-600 mb-8">Chúng tôi sẽ xem xét vấn đề của bạn và phản hồi trong thời gian sớm nhất.</p>

        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl p-5 mb-8 flex items-start">
            <i data-lucide="info" class="w-5 h-5 flex-shrink-0 mt-0.5 mr-3 text-blue-600"></i>
            <div>
                <p class="font-semibold text-blue-900 mb-1">Thông tin lớp học bị khiếu nại</p>
                <p class="text-sm">
                    Môn học: <span class="font-medium">{{ $lopHoc->monHoc->TenMon }}</span><br>
                    Khối lớp: <span class="font-medium">{{ $lopHoc->khoiLop->BacHoc }}</span><br>
                    Gia sư: <span class="font-medium">{{ $lopHoc->giaSu->HoTen ?? 'Chưa có' }}</span>
                </p>
            </div>
        </div>

        <form action="{{ route('nguoihoc.lophoc.complaint.store', $lopHoc->LopYeuCauID) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="NoiDung" class="block text-base font-semibold text-gray-800 mb-3">
                    Mô tả chi tiết vấn đề bạn gặp phải
                    <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="NoiDung" 
                    id="NoiDung" 
                    rows="8" 
                    class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 p-4 text-gray-700 placeholder-gray-400 text-base
                           @error('NoiDung') border-red-500 ring-red-500 @enderror" 
                    placeholder="Ví dụ: Gia sư thường xuyên đi muộn, chất lượng giảng dạy không đạt yêu cầu, hoặc có thái độ không phù hợp..."
                >{{ old('NoiDung') }}</textarea>
                
                @error('NoiDung')
                    <p class="text-sm text-red-600 mt-2 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('nguoihoc.lophoc.show', $lopHoc->LopYeuCauID) }}" class="px-7 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all">
                    Gửi khiếu nại
                </button>
            </div>
        </form>
    </div>
</div>
@endsection