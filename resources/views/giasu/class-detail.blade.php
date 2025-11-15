@extends('layouts.web')

@section('title', 'Chi tiết lớp học')

@section('content')

<div class="max-w-4xl mx-auto">
    {{-- Back button --}}
    <a href="{{ route('giasu.lophoc.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Quay lại danh sách
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ $lopHoc->monHoc->TenMon ?? 'Môn học' }}
                    @if($lopHoc->khoiLop)
                        - {{ $lopHoc->khoiLop->BacHoc }}
                    @endif
                </h1>
                <div class="flex items-center gap-3">
                    @php
                        $statusClass = match($lopHoc->TrangThai) {
                            'DangHoc' => 'bg-green-100 text-green-700',
                            'HoanThanh' => 'bg-gray-100 text-gray-700',
                            'TimGiaSu' => 'bg-blue-100 text-blue-700',
                            'Huy' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                        
                        $statusText = match($lopHoc->TrangThai) {
                            'DangHoc' => 'Đang dạy',
                            'HoanThanh' => 'Hoàn thành',
                            'TimGiaSu' => 'Tìm gia sư',
                            'Huy' => 'Đã hủy',
                            default => $lopHoc->TrangThai
                        };
                    @endphp
                    <span class="{{ $statusClass }} px-3 py-1 rounded-full text-sm font-bold">
                        {{ $statusText }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                        Tạo lúc: {{ \Carbon\Carbon::parse($lopHoc->NgayTao)->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Student Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="user" class="w-5 h-5 mr-2 text-blue-600"></i>
                Thông tin học sinh
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 w-32">Họ tên:</span>
                    <span class="font-semibold text-gray-900">
                        {{ $lopHoc->nguoiHoc->taiKhoan->HoTen ?? 'N/A' }}
                    </span>
                </div>
                @if($lopHoc->nguoiHoc->taiKhoan->Email)
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Email:</span>
                        <span class="text-gray-900">{{ $lopHoc->nguoiHoc->taiKhoan->Email }}</span>
                    </div>
                @endif
                @if($lopHoc->nguoiHoc->taiKhoan->SoDienThoai)
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Điện thoại:</span>
                        <span class="text-gray-900">{{ $lopHoc->nguoiHoc->taiKhoan->SoDienThoai }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Class Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="book-open" class="w-5 h-5 mr-2 text-green-600"></i>
                Thông tin lớp học
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 w-32">Môn học:</span>
                    <span class="font-semibold text-gray-900">{{ $lopHoc->monHoc->TenMon ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 w-32">Khối lớp:</span>
                    <span class="text-gray-900">{{ $lopHoc->khoiLop->BacHoc ?? 'N/A' }}</span>
                </div>
                @if($lopHoc->doiTuong)
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Đối tượng:</span>
                        <span class="text-gray-900">{{ $lopHoc->doiTuong->TenDoiTuong }}</span>
                    </div>
                @endif
                <div class="flex items-center">
                    <span class="text-gray-600 w-32">Hình thức:</span>
                    <span class="text-gray-900">{{ $lopHoc->HinhThuc ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule & Payment --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Schedule Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="clock" class="w-5 h-5 mr-2 text-purple-600"></i>
                Lịch học
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 w-32">Thời lượng:</span>
                    <span class="text-gray-900">{{ $lopHoc->ThoiLuong ?? 'N/A' }} giờ/buổi</span>
                </div>
                @if($lopHoc->SoBuoiTuan)
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Số buổi/tuần:</span>
                        <span class="text-gray-900">{{ $lopHoc->SoBuoiTuan }} buổi</span>
                    </div>
                @endif
                @if($lopHoc->LichHocMongMuon)
                    <div class="flex items-start">
                        <span class="text-gray-600 w-32">Lịch mong muốn:</span>
                        <span class="text-gray-900 flex-1">{{ $lopHoc->LichHocMongMuon }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="banknote" class="w-5 h-5 mr-2 text-green-600"></i>
                Thông tin học phí
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 w-32">Học phí:</span>
                    <span class="text-2xl font-bold text-green-600">
                        {{ number_format($lopHoc->HocPhi ?? 0, 0, ',', '.') }}đ
                    </span>
                </div>
                <div class="text-sm text-gray-500">
                    Học phí trên mỗi buổi học
                </div>
                @if($lopHoc->SoLuong && $lopHoc->SoLuong > 1)
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Số lượng:</span>
                        <span class="text-gray-900">{{ $lopHoc->SoLuong }} học sinh</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($lopHoc->MoTa)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="file-text" class="w-5 h-5 mr-2 text-gray-600"></i>
                Mô tả chi tiết
            </h2>
            <div class="text-gray-700 whitespace-pre-line">{{ $lopHoc->MoTa }}</div>
        </div>
    @endif

</div>

@endsection
