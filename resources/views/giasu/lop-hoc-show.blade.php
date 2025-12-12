@extends('layouts.web')

@section('title', 'Chi tiết lớp học')

@section('content')

@php
    $fromDashboard = request('from') === 'dashboard';
    $currentUser = Auth::user();
    $giaSu = $currentUser?->giaSu;
    $isDuyet = $giaSu && $giaSu->TrangThai == 1;
@endphp

<div class="max-w-6xl mx-auto relative"> {{-- Flash Messages (Giữ lại để hiển thị lỗi server nếu có) --}}
    @if(session('success'))
        <div class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce z-50">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ $fromDashboard ? route('giasu.dashboard') : route('giasu.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Quay lại {{ $fromDashboard ? 'trang chủ' : 'danh sách' }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
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
                                Khối {{ $lopHoc->khoiLop->BacHoc ?? $lopHoc->khoiLop->TenKhoi }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="text-right">
                            <p class="text-sm text-gray-500 mb-1">Học phí</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($lopHoc->HocPhi, 0, ',', '.') }}đ</p>
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
                        <span class="font-semibold text-gray-900">{{ $lopHoc->HinhThuc ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Thời lượng
                        </span>
                        <span class="font-semibold text-gray-900">{{ $lopHoc->ThoiLuong ? $lopHoc->ThoiLuong . ' phút/buổi' : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="calendar-days" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Số buổi/tuần
                        </span>
                        <span class="font-semibold text-gray-900">{{ $lopHoc->SoBuoiTuan ? $lopHoc->SoBuoiTuan . ' buổi' : 'N/A' }}</span>
                    </div>
                    @if($lopHoc->DiaChi)
                    <div class="flex items-start justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm flex items-center">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-gray-400"></i>
                            Địa chỉ
                        </span>
                        <span class="font-semibold text-gray-900 text-right max-w-xs">{{ $lopHoc->DiaChi }}</span>
                    </div>
                    @endif
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

            @if(isset($yeuCau) && $yeuCau)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 p-6">
                    <h3 class="text-base font-bold text-blue-900 mb-4 flex items-center">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                        Thông tin đề nghị
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700">Loại đề nghị</span>
                            @if($yeuCau->VaiTroNguoiGui === 'GiaSu')
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-semibold">Bạn đã gửi</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Nhận lời mời</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700">Trạng thái</span>
                            @if($yeuCau->TrangThai === 'ChoDuyet' || $yeuCau->TrangThai === 'Pending')
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Chờ duyệt</span>
                            @elseif($yeuCau->TrangThai === 'ChapNhan' || $yeuCau->TrangThai === 'Accepted')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Đã chấp nhận</span>
                            @elseif($yeuCau->TrangThai === 'TuChoi' || $yeuCau->TrangThai === 'Rejected')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Đã từ chối</span>
                            @endif
                        </div>
                        @if($yeuCau->GhiChu)
                        <div class="pt-3 border-t border-blue-200">
                            <p class="text-xs text-blue-600 mb-1">Ghi chú</p>
                            <p class="text-sm text-blue-900 italic">{{ $yeuCau->GhiChu }}</p>
                        </div>
                        @endif
                        <div class="pt-3 border-t border-blue-200 flex items-center justify-between">
                            <span class="text-xs text-blue-600">Ngày gửi</span>
                            <span class="text-sm text-blue-900 font-medium">
                                {{ $yeuCau->NgayTao instanceof \Carbon\Carbon ? $yeuCau->NgayTao->format('d/m/Y H:i') : \Carbon\Carbon::parse($yeuCau->NgayTao)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
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

                @if($lopHoc->TrangThai == 'DangHoc' && $lopHoc->GiaSuID == Auth::user()->giaSu->GiaSuID)
                    {{-- Nếu gia sư đang dạy lớp này --}}
                    <div class="mt-4">
                        <a href="{{ route('giasu.lophoc.schedule', $lopHoc->LopYeuCauID) }}" 
                           class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                            Xem lịch học
                        </a>
                    </div>
                @elseif($lopHoc->TrangThai == 'TimGiaSu' && !isset($yeuCau))
                    {{-- [SỬA ĐỔI] Sử dụng Modal thay vì submit trực tiếp --}}
                    <div class="mt-4">
                        @if($isDuyet)
                            <button type="button" 
                                onclick="openDeNghiModal({{ $lopHoc->LopYeuCauID }}, '{{ $lopHoc->monHoc->TenMon ?? 'Lớp học' }} - {{ $lopHoc->khoiLop->BacHoc ?? '' }}')"
                                class="block w-full text-center py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-lg">
                                <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                                Gửi đề nghị dạy
                            </button>
                        @else
                            <button disabled title="Tài khoản của bạn đang chờ duyệt" 
                                    class="block w-full text-center py-3 rounded-xl bg-gray-300 text-gray-500 font-bold cursor-not-allowed opacity-60">
                                <i data-lucide="lock" class="w-4 h-4 inline mr-2"></i>
                                Chờ duyệt
                            </button>
                        @endif
                    </div>
                @elseif(isset($yeuCau) && ($yeuCau->TrangThai == 'ChoDuyet' || $yeuCau->TrangThai == 'Pending'))
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

{{-- [THÊM MỚI] Modal Đề nghị dạy (Giống Dashboard) --}}
<div id="deNghiModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <form id="deNghiForm">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-lucide="send" class="h-6 w-6 text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Gửi đề nghị dạy
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Bạn muốn gửi đề nghị dạy lớp <span id="modalClassName" class="font-bold text-gray-900"></span>
                                </p>

                                <input type="hidden" name="lop_hoc_id" id="modalClassID">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú (tùy chọn)</label>
                                    <textarea name="ghi_chu" rows="4" maxlength="500" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none" 
                                              placeholder="Thêm ghi chú cho đề nghị của bạn (tối đa 500 ký tự)..."></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Ví dụ: giới thiệu kinh nghiệm, phương pháp giảng dạy...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Gửi đề nghị
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Hủy bỏ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- [THÊM MỚI] Scripts xử lý Modal và Ajax --}}
<script>
    function openDeNghiModal(lopHocId, lopHocName) {
        document.getElementById('modalClassID').value = lopHocId;
        document.getElementById('modalClassName').innerText = lopHocName;
        document.getElementById('deNghiModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('deNghiModal').classList.add('hidden');
        document.getElementById('deNghiForm').reset();
    }

    // Handle form submission with AJAX
    document.getElementById('deNghiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Đang gửi...';
        
        fetch('{{ route("giasu.de_nghi_day") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal();
                // Tải lại trang sau 1s để cập nhật trạng thái
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            console.error(error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Gửi đề nghị';
            lucide.createIcons();
        });
    });

    function showNotification(message, type) {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const notification = document.createElement('div');
        notification.className = `fixed bottom-5 right-5 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 4000);
    }
</script>

@endsection