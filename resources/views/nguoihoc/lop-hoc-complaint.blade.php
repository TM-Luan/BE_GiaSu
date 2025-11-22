@extends('layouts.web')

@section('title', 'Gửi khiếu nại')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('nguoihoc.lophoc.show', $lopHoc->LopYeuCauID) }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại chi tiết lớp
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Gửi khiếu nại</h1>
        <p class="text-gray-600 mb-8">Chúng tôi sẽ xem xét vấn đề của bạn và phản hồi trong thời gian sớm nhất.</p>

        {{-- Thông tin lớp học --}}
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

        {{-- Hiển thị Thông báo (Success / Error) --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start">
                <i data-lucide="x-circle" class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Form Gửi Khiếu Nại --}}
        <form action="{{ route('nguoihoc.lophoc.complaint.store', $lopHoc->LopYeuCauID) }}" method="POST" id="complaintForm">
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
                {{-- Nút Hủy --}}
                <a href="{{ route('nguoihoc.lophoc.show', $lopHoc->LopYeuCauID) }}" 
                   id="btnCancel"
                   class="px-7 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Quay lại
                </a>

                {{-- Nút Gửi / Cập nhật --}}
                <button type="submit" 
                        id="btnSubmit"
                        class="px-7 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(session('error')) disabled @endif>
                    Gửi khiếu nại
                </button>
            </div>
        </form>

        {{-- === PHẦN DANH SÁCH LỊCH SỬ KHIẾU NẠI === --}}
        @if(isset($lichSuKhieuNai) && $lichSuKhieuNai->count() > 0)
        <div class="mt-12 pt-8 border-t border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="history" class="w-5 h-5 mr-2 text-gray-500"></i>
                Lịch sử khiếu nại của bạn
            </h2>

            <div class="space-y-4">
                @foreach($lichSuKhieuNai as $item)
                    {{-- Logic: Cho phép sửa trong 5 phút và trạng thái 'TiepNhan' --}}
                    @php
                        $isTiepNhan = $item->TrangThai == 'TiepNhan';
                        $minutesLeft = 5 - \Carbon\Carbon::parse($item->NgayTao)->diffInMinutes(now());
                        $canEdit = $minutesLeft > 0 && $isTiepNhan;
                    @endphp

                    <div class="bg-gray-50 rounded-xl p-5 border {{ $canEdit ? 'border-blue-200 shadow-sm' : 'border-gray-200' }} transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center flex-wrap gap-2">
                                {{-- Badge Trạng Thái --}}
                                @php
                                    $statusColors = [
                                        'TiepNhan' => 'bg-yellow-100 text-yellow-800',
                                        'DangXuLy' => 'bg-blue-100 text-blue-800',
                                        'DaGiaiQuyet' => 'bg-green-100 text-green-800',
                                        'TuChoi' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusText = [
                                        'TiepNhan' => 'Đang tiếp nhận',
                                        'DangXuLy' => 'Đang xử lý',
                                        'DaGiaiQuyet' => 'Đã giải quyết',
                                        'TuChoi' => 'Đã từ chối',
                                    ];
                                    $color = $statusColors[$item->TrangThai] ?? 'bg-gray-100 text-gray-800';
                                    $text = $statusText[$item->TrangThai] ?? $item->TrangThai;
                                @endphp
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $color }}">
                                    {{ $text }}
                                </span>
                                <span class="text-xs text-gray-500 flex items-center">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($item->NgayTao)->format('H:i d/m/Y') }}
                                </span>
                            </div>
                            
                            {{-- Nút Thao Tác (Chỉ hiện khi $canEdit = true) --}}
                            @if($canEdit)
                            <div class="flex items-center gap-3">
                                {{-- Sửa --}}
                                <button type="button" 
                                        onclick="editComplaint({{ $item->KhieuNaiID }}, `{{ $item->NoiDung }}`)"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center transition-colors"
                                        title="Chỉnh sửa nội dung">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5 mr-1"></i> Sửa
                                </button>

                                {{-- Xóa --}}
                                <form action="{{ route('nguoihoc.lophoc.complaint.destroy', $item->KhieuNaiID) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn thu hồi khiếu nại này không?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold flex items-center transition-colors" title="Thu hồi khiếu nại">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1"></i> Thu hồi
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-3 text-gray-800 text-sm leading-relaxed whitespace-pre-line bg-white p-3 rounded-lg border border-gray-100">{{ $item->NoiDung }}</div>
                        
                        @if($canEdit)
                            <p class="text-xs text-blue-600 mt-2 font-medium flex items-center">
                                <i data-lucide="hourglass" class="w-3 h-3 mr-1"></i>
                                Bạn còn {{ $minutesLeft }} phút để chỉnh sửa hoặc thu hồi.
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Script Xử Lý Frontend --}}
<script>
    function editComplaint(id, content) {
        const form = document.getElementById('complaintForm');
        const txtNoiDung = document.getElementById('NoiDung');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnCancel = document.getElementById('btnCancel');

        // 1. Đổ dữ liệu vào form
        txtNoiDung.value = content;
        txtNoiDung.focus();

        // 2. Cuộn lên form mượt mà
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // 3. Thay đổi Action của Form để gọi API Update
        // Route mẫu: .../khieu-nai/{id} -> method PUT
        let updateUrl = "{{ route('nguoihoc.lophoc.complaint.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', id);
        form.action = updateUrl;

        // 4. Thêm input hidden _method = PUT
        // Xóa input cũ nếu có để tránh trùng lặp
        const existingMethod = form.querySelector('input[name="_method"]');
        if (existingMethod) existingMethod.remove();

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        // 5. Đổi giao diện nút bấm để người dùng nhận biết đang sửa
        btnSubmit.innerHTML = '<i data-lucide="save" class="w-4 h-4 inline mr-1"></i> Cập nhật khiếu nại';
        btnSubmit.classList.remove('bg-red-600', 'hover:bg-red-700', 'shadow-red-200');
        btnSubmit.classList.add('bg-blue-600', 'hover:bg-blue-700', 'shadow-blue-200');
        btnSubmit.disabled = false; // Bật lại nút nếu nó đang bị disable do lỗi trước đó

        // 6. Đổi nút "Quay lại" thành "Hủy sửa"
        btnCancel.innerText = "Hủy chế độ sửa";
        btnCancel.classList.add('text-red-600', 'border-red-200', 'bg-red-50');
        btnCancel.href = "javascript:window.location.reload()"; // Reload trang để reset form về ban đầu

        // Re-init icons nếu dùng lucide-js (để icon trong nút cập nhật hiện ra)
        if(typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
</script>
@endsection