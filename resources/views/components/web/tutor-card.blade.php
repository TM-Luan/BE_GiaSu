@props(['taikhoanGiaSu'])

@php
    $gs = $taikhoanGiaSu; 
    
    // 1. Xử lý Đánh giá
    $rating = round($gs->danh_gia_avg_diem_so ?? 0, 1);
    $reviewCount = $gs->danh_gia_count ?? 0;

    // 2. Xử lý Học phí
    $avgHocPhi = $gs->lopHocYeuCau->avg('HocPhi');
    
    if($avgHocPhi > 0) {
        $hienThiHocPhi = number_format($avgHocPhi, 0, ',', '.') . 'đ';
        $donVi = '/buổi';
    } else {
        $hienThiHocPhi = 'Liên hệ';
        $donVi = '';
    }

    // 3. Xử lý Địa chỉ
    $diaChi = $gs->DiaChi ?? 'Đang cập nhật';

    // 4. Xử lý Xác thực
    $isVerified = !empty($gs->AnhCCCD_MatTruoc);
@endphp

<div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 hover:border-blue-300 transition-all duration-300 group flex flex-col h-full relative overflow-hidden">
    
    <div class="absolute left-0 top-4 bottom-4 w-1 bg-blue-500 rounded-r-full opacity-0 group-hover:opacity-100 transition-opacity"></div>

    <div class="flex items-start gap-4 mb-3">
        <div class="relative flex-shrink-0">
            <img src="{{ $gs->AnhDaiDien ?? 'https://ui-avatars.com/api/?name='.urlencode($gs->HoTen).'&background=random&color=fff&size=96' }}"
                 alt="{{ $gs->HoTen }}"
                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-100 shadow-sm">
        </div>
        
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1">
                <h3 class="font-bold text-lg text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                    {{ $gs->HoTen }}
                </h3>
                @if($isVerified)
                    <i data-lucide="badge-check" class="w-5 h-5 text-blue-500 fill-blue-50 flex-shrink-0" title="Đã xác thực hồ sơ"></i>
                @endif
            </div>

            <p class="text-gray-500 text-sm font-medium truncate">
                {{ $gs->KinhNghiem ?? $gs->TruongDaoTao ?? 'Gia sư mới' }}
            </p>

            <div class="flex items-center gap-1 mt-1">
                <div class="flex text-yellow-400">
                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                </div>
                <span class="text-gray-900 font-bold text-sm">{{ $rating > 0 ? $rating : '---' }}</span>
                <span class="text-gray-400 text-xs">({{ $reviewCount }} đánh giá)</span>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 rounded-xl p-3 mb-4 space-y-2">
        <div class="flex items-start text-sm text-gray-600">
            <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="truncate">{{ $diaChi }}</span>
        </div>
        <div class="flex items-center text-sm text-gray-600">
            <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"></i>
            <span class="font-semibold text-green-600">
                {{ $hienThiHocPhi }}
                <span class="text-gray-400 font-normal text-xs">{{ $donVi }}</span>
            </span>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-6 mt-auto">
        @if($gs->ChuyenNganh)
            @foreach(explode(',', $gs->ChuyenNganh) as $index => $tag)
                @if($index < 3) 
                    <span class="bg-white border border-gray-200 text-gray-600 text-xs font-medium px-2.5 py-1 rounded-md">
                        {{ trim($tag) }}
                    </span>
                @endif
            @endforeach
            @if(count(explode(',', $gs->ChuyenNganh)) > 3)
                <span class="text-xs text-gray-400 py-1 px-1">+{{ count(explode(',', $gs->ChuyenNganh)) - 3 }}</span>
            @endif
        @else
             <span class="text-xs text-gray-400 italic">Đang cập nhật môn</span>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-3 mt-auto">
        <a href="{{ route('nguoihoc.giasu.show', ['id' => $gs->GiaSuID]) }}" 
        class="flex items-center justify-center py-2.5 px-4 rounded-xl text-gray-700 font-semibold bg-gray-100 hover:bg-gray-200 transition-colors text-sm group-hover:bg-blue-50 group-hover:text-blue-600">
            Xem hồ sơ
        </a>    
        <button type="button" 
                onclick="openInviteModal('{{ $gs->GiaSuID }}', '{{ $gs->HoTen }}')"
                class="py-2.5 px-4 rounded-xl text-white font-semibold bg-blue-600 hover:bg-blue-700 transition-colors text-sm shadow-md shadow-blue-200 cursor-pointer">
            Mời dạy
        </button>
    </div>
</div>