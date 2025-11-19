@extends('layouts.web')

@section('title', 'Chi tiết lớp học')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('giasu.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-48 bg-gradient-to-r from-blue-500 to-cyan-600 relative">
                    <div class="absolute bottom-0 left-0 p-6 w-full bg-gradient-to-t from-black/60 to-transparent">
                        <h1 class="text-3xl font-bold text-white">{{ $lopHoc->TieuDe }}</h1>
                        <p class="text-blue-100 mt-1 flex items-center">
                            <i data-lucide="book" class="w-4 h-4 mr-2"></i>
                            {{ $lopHoc->monHoc->TenMon ?? 'Môn học' }}
                        </p>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Mô tả lớp học</h3>
                    <div class="prose text-gray-600">
                        @if($lopHoc->MoTa)
                            {!! nl2br(e($lopHoc->MoTa)) !!}
                        @else
                            <em class="text-gray-400">Không có mô tả thêm.</em>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Thông tin lớp học</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Hình thức học</p>
                        <p class="font-semibold text-gray-800 flex items-center">
                            <i data-lucide="monitor" class="w-4 h-4 mr-2 text-blue-500"></i>
                            {{ $lopHoc->HinhThuc ?? 'Chưa cập nhật' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Học phí</p>
                        <p class="font-bold text-blue-600 text-lg flex items-center">
                            <i data-lucide="banknote" class="w-4 h-4 mr-2"></i>
                            {{ number_format($lopHoc->HocPhi, 0, ',', '.') }} VNĐ/buổi
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Thời lượng</p>
                        <p class="font-semibold text-gray-800">
                            {{ $lopHoc->ThoiLuong ? $lopHoc->ThoiLuong . ' phút/buổi' : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Số buổi/tuần</p>
                        <p class="font-semibold text-gray-800">
                            {{ $lopHoc->SoBuoiTuan ? $lopHoc->SoBuoiTuan . ' buổi' : 'N/A' }}
                        </p>
                    </div>
                    @if($lopHoc->DiaChi)
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm mb-1">Địa chỉ</p>
                            <p class="font-semibold text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <i data-lucide="map-pin" class="w-4 h-4 inline mr-2 text-red-500"></i>
                                {{ $lopHoc->DiaChi }}
                            </p>
                        </div>
                    @endif
                    @if($lopHoc->LichHocMongMuon)
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm mb-1">Lịch học</p>
                            <p class="font-semibold text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-2 text-orange-500"></i>
                                {{ $lopHoc->LichHocMongMuon }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Yêu cầu (nếu có) -->
            @if(isset($yeuCau) && $yeuCau)
                <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
                    <h3 class="text-lg font-bold text-blue-900 mb-3">Thông tin đề nghị</h3>
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <span class="text-blue-600 font-medium w-32">Loại:</span>
                            <span class="text-blue-900">
                                @if($yeuCau->VaiTroNguoiGui === 'GiaSu')
                                    <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-medium">Bạn đã gửi đề nghị</span>
                                @else
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">Nhận lời mời từ học viên</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-blue-600 font-medium w-32">Trạng thái:</span>
                            <span class="text-blue-900 font-semibold">
                                @if($yeuCau->TrangThai === 'ChoDuyet')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-medium">Chờ duyệt</span>
                                @elseif($yeuCau->TrangThai === 'ChapNhan')
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">Đã chấp nhận</span>
                                @elseif($yeuCau->TrangThai === 'TuChoi')
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">Đã từ chối</span>
                                @endif
                            </span>
                        </div>
                        @if($yeuCau->GhiChu)
                            <div class="flex items-start text-sm">
                                <span class="text-blue-600 font-medium w-32">Ghi chú:</span>
                                <span class="text-blue-900 italic">{{ $yeuCau->GhiChu }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm">
                            <span class="text-blue-600 font-medium w-32">Ngày gửi:</span>
                            <span class="text-blue-900">
                                {{ $yeuCau->NgayTao instanceof \Carbon\Carbon ? $yeuCau->NgayTao->format('d/m/Y H:i') : \Carbon\Carbon::parse($yeuCau->NgayTao)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Trạng thái -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-gray-900 font-bold mb-4">Trạng thái</h3>
                
                @php
                    $statusConfig = [
                        'TimGiaSu' => ['text' => 'Đang tìm gia sư', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'search'],
                        'DangHoc' => ['text' => 'Đang dạy', 'class' => 'bg-blue-100 text-blue-800 border-blue-200', 'icon' => 'book-open'],
                        'HoanThanh' => ['text' => 'Đã hoàn thành', 'class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'check-circle'],
                        'Huy' => ['text' => 'Đã hủy', 'class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'x-circle'],
                    ];
                    $status = $statusConfig[$lopHoc->TrangThai] ?? $statusConfig['TimGiaSu'];
                @endphp

                <div class="flex items-center justify-center p-4 rounded-xl border {{ $status['class'] }}">
                    <i data-lucide="{{ $status['icon'] }}" class="w-6 h-6 mr-2"></i>
                    <span class="font-bold text-lg">{{ $status['text'] }}</span>
                </div>

                <!-- Nút hành động -->
                @if($lopHoc->TrangThai == 'DangHoc' && $lopHoc->GiaSuID == Auth::user()->giaSu->GiaSuID)
                    {{-- Nếu gia sư đang dạy lớp này --}}
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('giasu.lophoc.schedule', $lopHoc->LopYeuCauID) }}" 
                           class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                            Xem lịch học
                        </a>
                        <a href="{{ route('giasu.lophoc.schedule.add', $lopHoc->LopYeuCauID) }}" 
                           class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                            Thêm lịch học
                        </a>
                    </div>
                @elseif($lopHoc->TrangThai == 'TimGiaSu' && !isset($yeuCau))
                    {{-- Nếu lớp đang tìm gia sư và chưa có yêu cầu --}}
                    <div class="mt-4">
                        <form action="{{ route('giasu.de_nghi_day') }}" method="POST">
                            @csrf
                            <input type="hidden" name="lop_yeu_cau_id" value="{{ $lopHoc->LopYeuCauID }}">
                            <button type="submit" 
                                    class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-lg">
                                <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                                Gửi đề nghị dạy
                            </button>
                        </form>
                    </div>
                @elseif(isset($yeuCau) && $yeuCau->TrangThai == 'ChoDuyet')
                    {{-- Nếu đã có yêu cầu đang chờ duyệt --}}
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-center">
                        <p class="text-yellow-800 font-medium">
                            @if($yeuCau->VaiTroNguoiGui === 'GiaSu')
                                Đã gửi đề nghị, đang chờ phản hồi
                            @else
                                Đã nhận lời mời từ học viên
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Thông tin học viên -->
            @if($lopHoc->nguoiHoc)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-gray-900 font-bold mb-4">Học viên</h3>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ $lopHoc->nguoiHoc->AnhDaiDien ? asset($lopHoc->nguoiHoc->AnhDaiDien) : 'https://ui-avatars.com/api/?name='.urlencode($lopHoc->nguoiHoc->HoTen) }}" 
                             class="w-14 h-14 rounded-full object-cover border border-gray-200">
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $lopHoc->nguoiHoc->HoTen }}</h4>
                            @if($lopHoc->nguoiHoc->DiaChi)
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                                    {{ $lopHoc->nguoiHoc->DiaChi }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($lopHoc->nguoiHoc->taiKhoan && $lopHoc->nguoiHoc->taiKhoan->SoDienThoai)
                        <div class="pt-4 border-t border-gray-100">
                            <a href="tel:{{ $lopHoc->nguoiHoc->taiKhoan->SoDienThoai }}" 
                               class="block w-full py-2.5 text-center text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="phone" class="w-4 h-4 inline mr-1"></i>
                                {{ $lopHoc->nguoiHoc->taiKhoan->SoDienThoai }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
