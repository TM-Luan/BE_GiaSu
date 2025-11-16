@extends('layouts.web')

@section('title', 'Chi tiết lớp học')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('nguoihoc.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-r from-blue-500 to-indigo-600 relative">
                    <div class="absolute bottom-0 left-0 p-6 w-full bg-gradient-to-t from-black/60 to-transparent">
                        <h1 class="text-3xl font-bold text-white">
                            {{ $lopHoc->monHoc->TenMon ?? 'Môn học' }} - {{ $lopHoc->khoiLop->BacHoc ?? '' }}
                        </h1>
                        <p class="text-blue-100 mt-1 flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                            Đăng ngày: {{ $lopHoc->NgayTao->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Mô tả chi tiết</h3>
                    <div class="prose text-gray-600">
                        @if($lopHoc->MoTa)
                            {!! nl2br(e($lopHoc->MoTa)) !!}
                        @else
                            <em class="text-gray-400">Không có mô tả thêm.</em>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Thông tin đăng ký</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Hình thức học</p>
                        <p class="font-semibold text-gray-800 flex items-center">
                            <i data-lucide="monitor" class="w-4 h-4 mr-2 text-blue-500"></i>
                            {{ $lopHoc->HinhThuc }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Học phí đề xuất</p>
                        <p class="font-bold text-green-600 text-lg flex items-center">
                            <i data-lucide="banknote" class="w-4 h-4 mr-2"></i>
                            {{ number_format($lopHoc->HocPhi) }} đ/buổi
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Thời lượng</p>
                        <p class="font-semibold text-gray-800">{{ $lopHoc->ThoiLuong }} phút/buổi</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Số buổi/tuần</p>
                        <p class="font-semibold text-gray-800">{{ $lopHoc->SoBuoiTuan }} buổi</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500 text-sm mb-1">Lịch học mong muốn</p>
                        <p class="font-semibold text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2 text-orange-500"></i>
                            {{ $lopHoc->LichHocMongMuon }}
                        </p>
                    </div>
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