@props(['lopHoc'])

@php
    $lop = $lopHoc;
    
    // 1. Thông tin cơ bản
    $monHoc = $lop->monHoc->TenMon ?? 'Chưa rõ môn học';
    $khoiLop = $lop->khoiLop->BacHoc ?? '';
    $tenLop = $monHoc . ($khoiLop ? " - {$khoiLop}" : '');
    
    // 2. Trạng thái
    $statusConfig = [
        'TimGiaSu' => ['text' => 'Đang tìm gia sư', 'class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'search'],
        'DangHoc' => ['text' => 'Đang học', 'class' => 'bg-green-100 text-green-800', 'icon' => 'book-open'],
        'HoanThanh' => ['text' => 'Đã hoàn thành', 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'check-circle'],
        'Huy' => ['text' => 'Đã hủy', 'class' => 'bg-red-100 text-red-700', 'icon' => 'x-circle'],
    ];
    $status = $statusConfig[$lop->TrangThai] ?? $statusConfig['TimGiaSu'];

    // 3. Đếm số lượng đề nghị (chỉ đếm của gia sư)
    $proposalCount = $lop->yeuCauNhanLops->where('VaiTroNguoiGui', 'GiaSu')->count();

    // 4. Thông tin gia sư (nếu có)
    $giaSu = $lop->giaSu;
    $tenGiaSu = $giaSu?->taiKhoan?->HoTen ?? null;

@endphp

<div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 hover:border-blue-300 transition-all duration-300 group flex flex-col h-full relative overflow-hidden">
    
    {{-- Left border accent on hover --}}
    <div class="absolute left-0 top-4 bottom-4 w-1 bg-blue-500 rounded-r-full opacity-0 group-hover:opacity-100 transition-opacity"></div>

    {{-- Header: Class title and status --}}
    <div class="mb-3">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="font-bold text-lg text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 flex-1">
                {{-- Link đến trang chi tiết lớp học --}}
                <a href="{{ route('nguoihoc.lophoc.show', $lop->LopYeuCauID) }}" class="hover:underline">
                    {{ $tenLop }}
                </a>
            </h3>
            {{-- Badge trạng thái --}}
            <span class="flex-shrink-0 {{ $status['class'] }} text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5">
                <i data-lucide="{{ $status['icon'] }}" class="w-3.5 h-3.5"></i>
                {{ $status['text'] }}
            </span>
        </div>
    </div>

    {{-- Description --}}
    @if($lop->MoTa)
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            {{ $lop->MoTa }}
        </p>
    @endif

    {{-- Info box --}}
    <div class="bg-gray-50 rounded-xl p-3 mb-4 space-y-2">
        {{-- Học phí --}}
        <div class="flex items-center text-sm text-gray-600">
            <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="font-semibold text-green-600">
                {{ number_format($lop->HocPhi, 0, ',', '.') }}đ
                <span class="text-gray-400 font-normal text-xs">/buổi</span>
            </span>
        </div>
        
        {{-- Hiển thị Số đề nghị (nếu đang tìm) hoặc Tên gia sư (nếu đang học) --}}
        @if($lop->TrangThai == 'TimGiaSu')
            <div class="flex items-center text-sm text-gray-600">
                <i data-lucide="users" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
                <span class="truncate">
                    Có <span class="font-bold text-blue-600">{{ $proposalCount }}</span> đề nghị
                </span>
            </div>
        @elseif($tenGiaSu)
             <div class="flex items-center text-sm text-gray-600">
                <i data-lucide="user-check" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
                <span class="truncate">
                    Gia sư: <span class="font-semibold text-gray-800">{{ $tenGiaSu }}</span>
                </span>
            </div>
        @endif

        {{-- Hình thức học --}}
        <div class="flex items-start text-sm text-gray-600">
            <i data-lucide="monitor" class="w-4 h-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="truncate">{{ $lop->HinhThuc }}</span>
        </div>
    </div>

    {{-- Các nút hành động (thay đổi theo trạng thái) --}}
    <div class="grid grid-cols-2 gap-3 mt-auto">
        @if($lop->TrangThai == 'TimGiaSu')
            {{-- Nút chính: Xem đề nghị --}}
            <a href="{{ route('nguoihoc.lophoc.proposals', $lop->LopYeuCauID) }}" 
               class="flex items-center justify-center py-2.5 px-4 rounded-xl text-white font-bold bg-blue-600 hover:bg-blue-700 transition-colors text-sm shadow-md hover:shadow-lg">
                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                Xem đề nghị
            </a>
            {{-- Nút phụ: Sửa lớp --}}
            <a href="{{ route('nguoihoc.lophoc.edit', $lop->LopYeuCauID) }}" 
               class="flex items-center justify-center py-2.5 px-4 rounded-xl text-gray-700 font-semibold bg-gray-100 hover:bg-gray-200 transition-colors text-sm group-hover:bg-blue-50 group-hover:text-blue-600">
                <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                Sửa
            </a>
        @else
            {{-- Nút xem chi tiết cho các trạng thái khác (Đang học, Hủy, Hoàn thành) --}}
            <a href="{{ route('nguoihoc.lophoc.show', $lop->LopYeuCauID) }}" 
               class="col-span-2 flex items-center justify-center py-2.5 px-4 rounded-xl text-white font-bold bg-blue-600 hover:bg-blue-700 transition-colors text-sm shadow-md hover:shadow-lg">
                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                Xem chi tiết
            </a>
        @endif
    </div>

    {{-- Ngày đăng --}}
    @if($lop->NgayTao)
        <div class="text-xs text-gray-400 mt-3 text-center">
            Đăng {{ \Carbon\Carbon::parse($lop->NgayTao)->diffForHumans() }}
        </div>
    @endif
</div>