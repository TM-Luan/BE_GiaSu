@props(['lopHoc'])

@php
    $lop = $lopHoc;
    
    // 1. Xử lý thông tin từ relations
    $monHoc = $lop->monHoc->TenMon ?? 'Chưa rõ môn học';
    $khoiLop = $lop->khoiLop->BacHoc ?? '';
    
    // 2. Xử lý tiêu đề - dùng tên môn làm tiêu đề
    $tenLop = $monHoc . ($khoiLop ? " - {$khoiLop}" : '');
    
    // 3. Mô tả
    $moTa = $lop->MoTa ?? '';
    
    // 4. Xử lý học phí
    $hocPhi = $lop->HocPhi ?? 0;
    $hienThiHocPhi = $hocPhi > 0 ? number_format($hocPhi, 0, ',', '.') . 'đ' : 'Liên hệ';
    
    // 5. Hình thức học
    $hinhThuc = $lop->HinhThuc ?? 'Chưa xác định';
    
    // 6. Thời lượng
    $thoiLuong = $lop->ThoiLuong ? $lop->ThoiLuong . ' giờ/buổi' : 'Linh hoạt';
    
    // 7. Xử lý thông tin học sinh
    $nguoiHoc = $lop->nguoiHoc ?? null;
    $tenNguoiHoc = $nguoiHoc?->taiKhoan?->HoTen ?? 'Học sinh';
@endphp

<div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 hover:border-green-300 transition-all duration-300 group flex flex-col h-full relative overflow-hidden">
    
    {{-- Left border accent on hover --}}
    <div class="absolute left-0 top-4 bottom-4 w-1 bg-green-500 rounded-r-full opacity-0 group-hover:opacity-100 transition-opacity"></div>

    {{-- Header: Class title and level --}}
    <div class="mb-3">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="font-bold text-lg text-gray-900 group-hover:text-green-600 transition-colors line-clamp-2 flex-1">
                {{ $tenLop }}
            </h3>
            @if($khoiLop)
                <span class="flex-shrink-0 bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">
                    {{ $khoiLop }}
                </span>
            @endif
        </div>

        {{-- Subject badge --}}
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center bg-purple-100 text-purple-700 text-sm font-semibold px-3 py-1 rounded-lg">
                <i data-lucide="book-open" class="w-4 h-4 mr-1"></i>
                {{ $monHoc }}
            </span>
        </div>
    </div>

    {{-- Description --}}
    @if($moTa)
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            {{ $moTa }}
        </p>
    @endif

    {{-- Info box --}}
    <div class="bg-gray-50 rounded-xl p-3 mb-4 space-y-2">
        {{-- Learning format --}}
        <div class="flex items-start text-sm text-gray-600">
            <i data-lucide="monitor" class="w-4 h-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="truncate">{{ $hinhThuc }}</span>
        </div>

        {{-- Fee --}}
        <div class="flex items-center text-sm text-gray-600">
            <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="font-semibold text-green-600">
                {{ $hienThiHocPhi }}
                <span class="text-gray-400 font-normal text-xs">/buổi</span>
            </span>
        </div>

        {{-- Duration --}}
        <div class="flex items-center text-sm text-gray-600">
            <i data-lucide="clock" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="truncate">{{ $thoiLuong }}</span>
        </div>

        {{-- Student info --}}
        <div class="flex items-center text-sm text-gray-600">
            <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="truncate">{{ $tenNguoiHoc }}</span>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="grid grid-cols-2 gap-3 mt-auto">
        {{-- View details button --}}
        <a href="#" 
           class="flex items-center justify-center py-2.5 px-4 rounded-xl text-gray-700 font-semibold bg-gray-100 hover:bg-gray-200 transition-colors text-sm group-hover:bg-green-50 group-hover:text-green-600">
            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
            Chi tiết
        </a>
        
        {{-- Propose button --}}
        <button type="button" 
                onclick="openDeNghiModal({{ $lop->LopYeuCauID }}, '{{ addslashes($tenLop) }}')"
                class="flex items-center justify-center py-2.5 px-4 rounded-xl text-white font-bold bg-green-600 hover:bg-green-700 transition-colors text-sm shadow-md hover:shadow-lg">
            <i data-lucide="send" class="w-4 h-4 mr-1"></i>
            Đề nghị dạy
        </button>
    </div>

    {{-- Posted date (small footer) --}}
    @if($lop->NgayTao)
        <div class="text-xs text-gray-400 mt-3 text-center">
            Đăng {{ \Carbon\Carbon::parse($lop->NgayTao)->diffForHumans() }}
        </div>
    @endif
</div>
