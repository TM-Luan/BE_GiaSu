@extends('layouts.web')

@section('title', 'Lớp học của tôi')

@section('content')

    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quản lý lớp học</h1>
        <p class="text-gray-500 mt-2 text-base font-medium">Theo dõi các lớp đang dạy, lịch sử và lời mời dạy.</p>
    </div>

    {{-- THANH TAB NAVIGATION --}}
    <div class="mb-8 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            {{-- Tab 1: Đang dạy --}}
            <a href="{{ route('giasu.lophoc.index', ['tab' => 'dang_day']) }}"
               class="{{ $currentTab == 'dang_day' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base transition-colors flex items-center">
                <i data-lucide="book-open" class="w-5 h-5 mr-2"></i>
                Đang dạy
                @if($lopDangDay->total() > 0)
                    <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2.5 rounded-full text-xs font-bold">
                        {{ $lopDangDay->total() }}
                    </span>
                @endif
            </a>

            {{-- Tab 2: Đã dạy --}}
            <a href="{{ route('giasu.lophoc.index', ['tab' => 'da_day']) }}"
               class="{{ $currentTab == 'da_day' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base transition-colors flex items-center">
                <i data-lucide="archive" class="w-5 h-5 mr-2"></i>
                Đã dạy (Lịch sử)
            </a>

            {{-- Tab 3: Lời mời --}}
            <a href="{{ route('giasu.lophoc.index', ['tab' => 'loi_moi']) }}"
               class="{{ $currentTab == 'loi_moi' 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base transition-colors flex items-center">
                <i data-lucide="bell" class="w-5 h-5 mr-2"></i>
                Lời mời & Đề nghị
                @if($yeuCauDeNghi->total() > 0)
                    <span class="ml-2 bg-yellow-100 text-yellow-700 py-0.5 px-2.5 rounded-full text-xs font-bold">
                        {{ $yeuCauDeNghi->total() }}
                    </span>
                @endif
            </a>
        </nav>
    </div>

    {{-- NỘI DUNG TƯƠNG ỨNG VỚI TAB --}}
    
    {{-- 1. CONTENT: ĐANG DẠY --}}
    @if($currentTab == 'dang_day')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($lopDangDay as $lop)
                @php
                    $daThanhToan = $lop->TrangThaiThanhToan === 'DaThanhToan';
                    // --- SỬA LỖI TÊN LỚP ---
                    $tenMon = $lop->monHoc->TenMon ?? 'Môn học';
                    $tenKhoi = $lop->khoiLop->TenKhoiLop ?? '';
                    $hienThiTenLop = $tenMon . ($tenKhoi ? " - $tenKhoi" : '');
                @endphp
                
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col h-full overflow-hidden">
                    {{-- Header Card --}}
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start mb-3">
                            {{-- Hiển thị tên lớp đã sửa --}}
                            <h3 class="font-bold text-lg text-gray-900 line-clamp-2" title="{{ $hienThiTenLop }}">
                                {{ $hienThiTenLop }}
                            </h3>
                            @if($daThanhToan)
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded whitespace-nowrap ml-2">
                                    Đang dạy
                                </span>
                            @else
                                <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded whitespace-nowrap ml-2">
                                    Chưa thanh toán
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <div class="flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span class="font-medium text-gray-900">{{ $lop->nguoiHoc->HoTen ?? 'Học viên' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span class="text-blue-600 font-semibold">{{ number_format($lop->HocPhi) }}đ/buổi</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span>{{ $lop->SoBuoiTuan }} buổi/tuần</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Card: CÁC NÚT CHỨC NĂNG --}}
                    <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 grid gap-2">
                        @if($daThanhToan)
                            {{-- Đã thanh toán: --}}
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('giasu.lophoc.schedule.create', $lop->LopYeuCauID) }}" 
                                   class="flex items-center justify-center px-3 py-2 bg-white border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 font-medium text-sm transition">
                                    <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i> Tạo lịch
                                </a>
                                <a href="{{ route('giasu.lophoc.show', $lop->LopYeuCauID) }}"
                                   class="flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition">
                                    <i  class="w-4 h-4 mr-1"></i> Xem chi tiết 
                                </a>
                            </div>
                             {{-- Nút xem chi tiết --}}
                            
                                
           
                            {{-- Hàng 2: Hủy lịch & Hoàn thành --}}
                            <div class="grid grid-cols-2 gap-2">
                                {{-- Nút Hủy Lịch (MỚI THÊM) --}}
                                <form action="{{ route('giasu.lophoc.schedule.delete-all', $lop->LopYeuCauID) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ xóa TOÀN BỘ lịch học hiện tại để bạn tạo lại từ đầu. Bạn có chắc không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full flex items-center justify-center px-3 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 font-medium text-sm transition">
                                        <i data-lucide="trash" class="w-4 h-4 mr-1"></i> Hủy lịch
                                    </button>
                                </form>

                                {{-- Nút Hoàn thành --}}
                                <form action="{{ route('giasu.lophoc.complete', $lop->LopYeuCauID) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn đã hoàn thành khóa học này? Lớp sẽ được chuyển sang lịch sử.');">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition shadow-sm shadow-green-200">
                                        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Hoàn thành
                                    </button>
                                </form>
                            </div>

                           
                        @else
                            {{-- Chưa thanh toán: Hiện nút Thanh toán & Hủy lớp --}}
                            <a href="{{ route('giasu.lophoc.payment', $lop->LopYeuCauID) }}" 
                               class="flex items-center justify-center w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-bold text-sm transition shadow-sm shadow-orange-200">
                                <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i> Thanh toán phí nhận lớp
                            </a>
                            
                            <div class="flex justify-between items-center mt-1">
                                <a href="{{ route('giasu.lophoc.show', $lop->LopYeuCauID) }}" class="text-sm text-gray-500 hover:text-gray-800">
                                    Chi tiết
                                </a>
                                <form action="{{ route('giasu.lophoc.cancel', $lop->LopYeuCauID) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy lớp này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium flex items-center">
                                        <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i> Hủy lớp
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <div class="bg-white p-4 rounded-full mb-4 shadow-sm">
                        <i data-lucide="book" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-lg font-medium">Bạn chưa có lớp học nào đang dạy.</p>
                    <a href="{{ route('giasu.dashboard') }}" class="text-blue-600 hover:underline mt-2 font-medium">
                        Tìm lớp mới ngay
                    </a>
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $lopDangDay->appends(['tab' => 'dang_day'])->links() }}
        </div>
    @endif

    {{-- 2. CONTENT: ĐÃ DẠY (CHỈ CÓ NÚT XEM CHI TIẾT) --}}
    @if($currentTab == 'da_day')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-80">
            @forelse($lopDaDay as $lop)
                @php
                    // --- SỬA LỖI TÊN LỚP CHO TAB ĐÃ DẠY ---
                    $tenMon = $lop->monHoc->TenMon ?? 'Môn học';
                    $tenKhoi = $lop->khoiLop->TenKhoiLop ?? '';
                    $hienThiTenLop = $tenMon . ($tenKhoi ? " - $tenKhoi" : '');
                @endphp
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col h-full overflow-hidden">
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-lg text-gray-600 line-clamp-2" title="{{ $hienThiTenLop }}">
                                {{ $hienThiTenLop }}
                            </h3>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded whitespace-nowrap ml-2">
                                {{ $lop->TrangThai == 'HoanThanh' ? 'Hoàn thành' : 'Đã kết thúc' }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span class="font-medium">{{ $lop->nguoiHoc->HoTen ?? 'Học viên' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span>{{ number_format($lop->HocPhi) }}đ/buổi</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="clock" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span>Kết thúc: {{ $lop->updated_at ? $lop->updated_at->format('d/m/Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer: CHỈ CÓ NÚT XEM CHI TIẾT --}}
                    <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
                        <a href="{{ route('giasu.lophoc.show', $lop->LopYeuCauID) }}" 
                           class="flex items-center justify-center w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium text-sm transition">
                            <i data-lucide="eye" class="w-4 h-4 mr-2"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <div class="bg-white p-4 rounded-full mb-4 shadow-sm">
                        <i data-lucide="clock" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-lg font-medium">Lịch sử dạy học trống.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $lopDaDay->appends(['tab' => 'da_day'])->links() }}
        </div>
    @endif

    {{-- 3. CONTENT: LỜI MỜI / ĐỀ NGHỊ --}}
    @if($currentTab == 'loi_moi')
        <div class="space-y-4">
            @forelse($yeuCauDeNghi as $proposal)
                @php
                    $lop = $proposal->lop;
                    $isGiaSuSent = $proposal->VaiTroNguoiGui === 'GiaSu';
                    $tenHocSinh = $lop->nguoiHoc->HoTen ?? 'Học sinh';
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-blue-300 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    {{-- Thông tin bên trái --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-bold text-lg text-gray-900">
                                {{ $lop->monHoc->TenMon ?? 'Môn học' }} 
                                <span class="font-normal text-gray-500">- {{ $lop->khoiLop->TenKhoiLop ?? '' }}</span>
                            </h3>
                            @if($isGiaSuSent)
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-xs font-medium border border-blue-100">
                                    Bạn đã ứng tuyển
                                </span>
                            @else
                                <span class="bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full text-xs font-medium border border-green-100">
                                    Lời mời dạy
                                </span>
                            @endif
                            <span class="bg-yellow-50 text-yellow-700 px-2.5 py-0.5 rounded-full text-xs font-medium border border-yellow-100">
                                Chờ duyệt
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-6 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                                {{ $tenHocSinh }}
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400"></i>
                                {{ number_format($lop->HocPhi) }}đ/buổi
                            </div>
                            <div class="flex items-center text-gray-400">
                                <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                {{ $proposal->NgayTao->format('H:i d/m/Y') }}
                            </div>
                        </div>

                        @if($proposal->GhiChu)
                            <div class="mt-3 bg-gray-50 p-3 rounded-lg text-sm text-gray-600 italic border border-gray-100 inline-block">
                                "{{ $proposal->GhiChu }}"
                            </div>
                        @endif
                    </div>

                    {{-- Nút thao tác bên phải --}}
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <a href="{{ route('giasu.lophoc.show', $lop->LopYeuCauID) }}" class="flex-1 md:flex-none px-4 py-2 text-center border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition text-sm">
                            Xem chi tiết
                        </a>
                        
                        @if($isGiaSuSent)
                            {{-- Nếu Gia sư gửi -> Nút Hủy --}}
                            <form action="{{ route('giasu.lophoc.cancel_proposal', $proposal->YeuCauID) }}" method="POST" class="flex-1 md:flex-none" onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition text-sm">
                                    Hủy yêu cầu
                                </button>
                            </form>
                        @else
                            {{-- Nếu Học viên mời -> Nút Chấp nhận / Từ chối --}}
                            <form action="{{ route('giasu.lophoc.reject_invitation', $proposal->YeuCauID) }}" method="POST" class="inline-block" onsubmit="return confirm('Từ chối lời mời này?');">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg font-medium hover:bg-red-50 transition text-sm">
                                    Từ chối
                                </button>
                            </form>
                            <form action="{{ route('giasu.lophoc.accept_invitation', $proposal->YeuCauID) }}" method="POST" class="inline-block" onsubmit="return confirm('Chấp nhận dạy lớp này?');">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm shadow-sm shadow-blue-200">
                                    Chấp nhận
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <div class="bg-white p-4 rounded-full mb-4 shadow-sm">
                        <i data-lucide="bell-off" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-lg font-medium">Không có lời mời hay đề nghị nào.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $yeuCauDeNghi->appends(['tab' => 'loi_moi'])->links() }}
        </div>
    @endif

@endsection