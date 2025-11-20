@extends('layouts.web')

@section('title', 'Lớp học của tôi - Gia sư')

@section('content')
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center">
                <div class="bg-blue-100 p-3 rounded-xl mr-4">
                    <i data-lucide="school" class="w-8 h-8 text-blue-600"></i>
                </div>
                Lớp học của tôi
            </h1>
            <p class="text-gray-500 mt-2 text-base font-medium ml-16">Quản lý các lớp đang dạy và đề nghị nhận lớp.</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <a href="{{ route('giasu.lophoc.index', ['tab' => 'danghoc']) }}" 
                   class="group inline-flex items-center py-4 px-3 border-b-2 font-medium text-sm transition-all {{ $tab === 'danghoc' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2 {{ $tab === 'danghoc' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Đang dạy
                    <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ $tab === 'danghoc' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ $lopDangDay->total() }}
                    </span>
                </a>
                <a href="{{ route('giasu.lophoc.index', ['tab' => 'denghi']) }}" 
                   class="group inline-flex items-center py-4 px-3 border-b-2 font-medium text-sm transition-all {{ $tab === 'denghi' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i data-lucide="pending" class="w-5 h-5 mr-2 {{ $tab === 'denghi' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Đề nghị
                    <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ $tab === 'denghi' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ $yeuCauDeNghi->total() }}
                    </span>
                </a>
            </nav>
        </div>

        <!-- Thông báo -->
        @if(session('success'))
            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-3 text-blue-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-3 text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- TAB CONTENT -->
        @if($tab === 'danghoc')
            <!-- TAB 1: LỚP ĐANG DẠY -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($lopDangDay as $lopHoc)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center flex-1">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-lg leading-tight">
                                            {{ $lopHoc->monHoc->TenMon ?? 'N/A' }}
                                            @if($lopHoc->khoiLop)
                                                - {{ $lopHoc->khoiLop->BacHoc }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-0.5">{{ $lopHoc->MoTa ?? '' }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    Đang dạy
                                </span>
                            </div>

                            <!-- Thông tin -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                                    <span class="font-medium">Học viên:</span>
                                    <span class="ml-2">{{ $lopHoc->nguoiHoc->HoTen ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i data-lucide="dollar-sign" class="w-4 h-4 mr-2 text-gray-400"></i>
                                    <span class="font-medium">Học phí:</span>
                                    <span class="ml-2 text-blue-600 font-semibold">{{ number_format($lopHoc->HocPhi, 0, ',', '.') }} VNĐ/buổi</span>
                                </div>
                                @if($lopHoc->DiaChi)
                                    <div class="flex items-start text-sm text-gray-600">
                                        <i data-lucide="map-pin" class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0"></i>
                                        <span>{{ $lopHoc->DiaChi }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <a href="{{ route('giasu.lophoc.show', $lopHoc->LopYeuCauID) }}" 
                                   class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                    Xem chi tiết
                                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                                </a>
                                <a href="{{ route('giasu.lophoc.schedule', $lopHoc->LopYeuCauID) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-1.5"></i>
                                    Lịch học
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 flex flex-col items-center justify-center py-16 text-gray-500 bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="bg-gray-100 p-4 rounded-full mb-4">
                            <i data-lucide="book-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium">Bạn chưa có lớp nào đang dạy</p>
                        <p class="text-sm mt-1">Kiểm tra tab "Đề nghị" để nhận lớp mới!</p>
                    </div>
                @endforelse
            </div>

            <!-- Phân trang -->
            @if($lopDangDay->hasPages())
                <div class="mt-8">
                    {{ $lopDangDay->appends(['tab' => 'danghoc'])->links() }}
                </div>
            @endif

        @else
            <!-- TAB 2: ĐỀ NGHỊ -->
            <div class="space-y-4">
                @forelse($yeuCauDeNghi as $yeuCau)
                    @php
                        // Đồng bộ với API mobile: dùng relation 'lop' thay vì 'lophoc'
                        $lopHoc = $yeuCau->lop;
                        $isReceived = $yeuCau->VaiTroNguoiGui === 'NguoiHoc'; // Học viên mời
                        $isSent = $yeuCau->VaiTroNguoiGui === 'GiaSu'; // Gia sư gửi
                        $trangThai = $yeuCau->TrangThai;
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <!-- Nội dung chính -->
                                <div class="flex-1">
                                    <!-- Header -->
                                    <div class="flex items-start mb-3">
                                        <div class="bg-{{ $isReceived ? 'blue' : 'orange' }}-100 p-2 rounded-lg mr-3">
                                            <i data-lucide="{{ $isReceived ? 'mail' : 'send' }}" class="w-5 h-5 text-{{ $isReceived ? 'blue' : 'orange' }}-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 text-lg leading-tight">
                                                {{ $lopHoc->monHoc->TenMon ?? 'N/A' }}
                                                @if($lopHoc->khoiLop)
                                                    - {{ $lopHoc->khoiLop->BacHoc }}
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-0.5">{{ $lopHoc->MoTa ?? '' }}</p>
                                        </div>
                                        <div class="ml-3">
                                            @if($trangThai === 'Pending')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    Chờ duyệt
                                                </span>
                                            @elseif($trangThai === 'Rejected')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    Đã từ chối
                                                </span>
                                            @elseif($trangThai === 'Cancelled')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                    Đã hủy
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Type Label -->
                                    <div class="mb-3">
                                        @if($isReceived)
                                            <span class="inline-flex items-center text-sm text-blue-600 font-medium">
                                                <i data-lucide="arrow-down-left" class="w-4 h-4 mr-1"></i>
                                                Nhận được lời mời dạy từ học viên
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-sm text-orange-600 font-medium">
                                                <i data-lucide="arrow-up-right" class="w-4 h-4 mr-1"></i>
                                                Bạn đã gửi đề nghị dạy
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Thông tin -->
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                                            <span class="font-medium">Học viên:</span>
                                            <span class="ml-2">{{ $lopHoc->nguoihoc->HoTen ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-2 text-gray-400"></i>
                                            <span class="font-medium">Học phí:</span>
                                            <span class="ml-2 text-blue-600 font-semibold">{{ number_format($lopHoc->HocPhi, 0, ',', '.') }} VNĐ/buổi</span>
                                        </div>
                                        @if($yeuCau->GhiChu)
                                            <div class="flex items-start text-sm text-gray-600">
                                                <i data-lucide="message-square" class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0"></i>
                                                <span class="italic">{{ $yeuCau->GhiChu }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
                                <!-- Nút Xem chi tiết -->
                                <a href="{{ route('giasu.lophoc.show', $lopHoc->LopYeuCauID) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1.5"></i>
                                    Xem chi tiết
                                </a>

                                @if($trangThai === 'Pending')
                                    @if($isReceived)
                                        <!-- Học viên mời → Gia sư có thể Chấp nhận/Từ chối -->
                                        <form action="{{ route('giasu.lophoc.invitation.reject', $yeuCau->YeuCauID) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Bạn chắc chắn muốn từ chối lời mời này?')"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                <i data-lucide="x" class="w-4 h-4 mr-1.5"></i>
                                                Từ chối
                                            </button>
                                        </form>
                                        <form action="{{ route('giasu.lophoc.invitation.accept', $yeuCau->YeuCauID) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Bạn chắc chắn muốn chấp nhận dạy lớp này?')"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                                <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                                                Chấp nhận
                                            </button>
                                        </form>
                                    @else
                                        <!-- Gia sư gửi → Có thể Hủy -->
                                        <form action="{{ route('giasu.lophoc.proposal.cancel', $yeuCau->YeuCauID) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Bạn chắc chắn muốn hủy đề nghị này?')"
                                                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4 mr-1.5"></i>
                                                Hủy đề nghị
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-gray-500 bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="bg-gray-100 p-4 rounded-full mb-4">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium">Chưa có đề nghị nào</p>
                        <p class="text-sm mt-1">Đề nghị dạy hoặc lời mời từ học viên sẽ hiện ở đây!</p>
                    </div>
                @endforelse
            </div>

            <!-- Phân trang -->
            @if($yeuCauDeNghi->hasPages())
                <div class="mt-8">
                    {{ $yeuCauDeNghi->appends(['tab' => 'denghi'])->links() }}
                </div>
            @endif
        @endif

    </div>
@endsection

@push('scripts')
<script>
    // Auto-hide success/error messages
    setTimeout(() => {
        const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
