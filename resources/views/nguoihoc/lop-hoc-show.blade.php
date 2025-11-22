@extends('layouts.web')

@section('title', 'Chi tiết lớp học')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('nguoihoc.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Quay lại danh sách
        </a>
    </div>
    {{-- === THÊM ĐOẠN NÀY VÀO ĐÂY === --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center">
            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
            <strong>{{ session('error') }}</strong>
        </div>
    @endif
    {{-- ============================== --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card - Simplified -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $lopHoc->TieuDe }}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span class="inline-flex items-center">
                                <i data-lucide="book" class="w-4 h-4 mr-1.5 text-blue-500"></i>
                                {{ $lopHoc->monHoc->TenMon ?? 'Môn học' }}
                            </span>
                            @if($lopHoc->khoiLop)
                            <span class="inline-flex items-center">
                                <i data-lucide="graduation-cap" class="w-4 h-4 mr-1.5 text-purple-500"></i>
                                {{ $lopHoc->khoiLop->TenKhoi ?? 'Khối lớp' }}
                            </span>
                            @endif
                            <span class="inline-flex items-center text-gray-500">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                {{ $lopHoc->NgayTao->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="text-right">
                            <p class="text-sm text-gray-500 mb-1">Học phí</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($lopHoc->HocPhi, 0, ',', '.') }}đ</p>
                            <p class="text-xs text-gray-500">/ buổi</p>
                        </div>
                    </div>
                </div>
                
                @if($lopHoc->MoTa)
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Mô tả lớp học</h3>
                    <div class="text-gray-600 text-sm leading-relaxed">
                        {!! nl2br(e($lopHoc->MoTa)) !!}
                    </div>
                </div>
                @endif
            </div>

            <!-- Thông tin chi tiết - Simplified -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Thông tin chi tiết
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="monitor" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Hình thức học
                        </span>
                        <span class="font-semibold text-gray-900">{{ $lopHoc->HinhThuc }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Thời lượng
                        </span>
                        <span class="font-semibold text-gray-900">{{ $lopHoc->ThoiLuong }} phút/buổi</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="calendar-days" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Số buổi/tuần
                        </span>
                        <span class="font-semibold text-gray-900">{{ $lopHoc->SoBuoiTuan }} buổi</span>
                    </div>
                    @if($lopHoc->LichHocMongMuon)
                    <div class="flex items-start justify-between py-2">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Lịch học mong muốn
                        </span>
                        <span class="font-semibold text-gray-900 text-right max-w-xs">{{ $lopHoc->LichHocMongMuon }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-gray-900 font-bold mb-4">Trạng thái lớp học</h3>
                
                @php
                    $statusConfig = [
                        'TimGiaSu' => ['text' => 'Đang tìm gia sư', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'search'],
                        'DangHoc' => ['text' => 'Đang học', 'class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'book-open'],
                        'HoanThanh' => ['text' => 'Đã hoàn thành', 'class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'check-circle'],
                        'Huy' => ['text' => 'Đã hủy', 'class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'x-circle'],
                    ];
                    $status = $statusConfig[$lopHoc->TrangThai] ?? $statusConfig['TimGiaSu'];
                @endphp

                <div class="flex items-center justify-center p-4 rounded-xl border {{ $status['class'] }} mb-6">
                    <i data-lucide="{{ $status['icon'] }}" class="w-6 h-6 mr-2"></i>
                    <span class="font-bold text-lg">{{ $status['text'] }}</span>
                </div>

                @if($lopHoc->TrangThai == 'TimGiaSu')
                    <a href="{{ route('nguoihoc.lophoc.proposals', $lopHoc->LopYeuCauID) }}" class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors mb-3">
                        Xem danh sách đề nghị ({{ $lopHoc->yeuCauNhanLops->where('VaiTroNguoiGui', 'GiaSu')->count() }})
                    </a>
                    <a href="{{ route('nguoihoc.lophoc.edit', $lopHoc->LopYeuCauID) }}" class="block w-full text-center py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors mb-3">
                        Sửa thông tin
                    </a>

                    <form action="{{ route('nguoihoc.lophoc.cancel', $lopHoc->LopYeuCauID) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lớp học này?');">
                        @csrf
                        <button type="submit" class="block w-full text-center py-3 rounded-xl bg-red-100 text-red-700 font-bold hover:bg-red-200 transition-colors">
                            Hủy lớp học
                        </button>
                    </form>
                    @endif
            </div>

            @if($lopHoc->giaSu)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-gray-900 font-bold mb-4">Gia sư phụ trách</h3>
                
                <div class="flex items-center gap-4">
                    <img src="{{ $lopHoc->giaSu->AnhDaiDien ? asset($lopHoc->giaSu->AnhDaiDien) : 'https://ui-avatars.com/api/?name='.urlencode($lopHoc->giaSu->HoTen) }}" 
                         class="w-14 h-14 rounded-full object-cover border border-gray-200">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $lopHoc->giaSu->HoTen }}</h4>
                        <p class="text-sm text-gray-500">{{ $lopHoc->giaSu->ChuyenNganh ?? 'Chưa cập nhật chuyên ngành' }}</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('nguoihoc.giasu.show', $lopHoc->GiaSuID) }}" class="block w-full py-2.5 text-center text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Xem hồ sơ
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection