@extends('layouts.web')

@section('title', 'Tạo lịch học tự động')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-6">
        <a href="{{ route('giasu.lophoc.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg mr-4">
                    <i data-lucide="calendar-plus" class="w-8 h-8"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Tạo lịch học tự động</h1>
                    <p class="text-blue-100 mt-1">Thiết lập lịch học định kỳ cho lớp</p>
                </div>
            </div>
        </div>

        <!-- Thông tin lớp học -->
        <div class="p-6 border-b border-gray-200 bg-blue-50">
            <h3 class="font-bold text-lg text-gray-900 mb-3">{{ $lopHoc->monHoc->TenMon ?? 'N/A' }} - {{ $lopHoc->khoiLop->BacHoc ?? 'N/A' }}</h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Học viên</p>
                    <p class="font-semibold">{{ $lopHoc->nguoiHoc->HoTen ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Thời lượng</p>
                    <p class="font-semibold">{{ $lopHoc->ThoiLuong ?? 90 }} phút</p>
                </div>
                <div>
                    <p class="text-gray-600">Số buổi/tuần</p>
                    <p class="font-semibold">{{ $lopHoc->SoBuoiTuan ?? 2 }} buổi</p>
                </div>
            </div>

            @php
                $isPaid = $lopHoc->TrangThaiThanhToan === 'Paid';
                $phiNhanLop = $lopHoc->HocPhi * ($lopHoc->SoBuoiTuan ?? 2) * 4 * 0.3;
            @endphp

            @if(!$isPaid)
            <!-- Cảnh báo chưa thanh toán -->
            <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <div class="flex items-start">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-orange-600 mr-3 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold text-orange-800">Chưa thanh toán phí nhận lớp</p>
                        <p class="text-sm text-orange-700 mt-1">Bạn cần thanh toán <span class="font-bold">{{ number_format($phiNhanLop, 0, ',', '.') }} VNĐ</span> để hoàn tất việc tạo lịch học. Sau khi nhấn nút "Thanh toán & Tạo lịch", bạn sẽ được chuyển đến trang thanh toán.</p>
                    </div>
                </div>
            </div>
            @endif

            @if($lopHoc->LichHocMongMuon)
            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-gray-700"><span class="font-semibold">Lịch mong muốn:</span> {{ $lopHoc->LichHocMongMuon }}</p>
            </div>
            @endif
        </div>

        <!-- Form tạo lịch -->
        <form action="{{ route('giasu.lophoc.schedule.store', $lopHoc->LopYeuCauID) }}" method="POST" class="p-6">
            @csrf

            <!-- Ngày bắt đầu -->
            <div class="mb-6">
                <label class="block font-semibold text-gray-900 mb-2">
                    <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                    Ngày bắt đầu
                </label>
                <input type="date" name="ngay_bat_dau" value="{{ old('ngay_bat_dau', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                @error('ngay_bat_dau')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Số tuần -->
            <div class="mb-6">
                <label class="block font-semibold text-gray-900 mb-2">
                    <i data-lucide="repeat" class="w-4 h-4 inline mr-2"></i>
                    Số tuần muốn tạo
                </label>
                <select name="so_tuan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="4" selected>4 tuần (1 tháng)</option>
                    <option value="8">8 tuần (2 tháng)</option>
                    <option value="12">12 tuần (3 tháng)</option>
                    <option value="24">24 tuần (6 tháng)</option>
                </select>
                @error('so_tuan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Đường dẫn học online -->
            @if($lopHoc->HinhThuc === 'Online')
            <div class="mb-6">
                <label class="block font-semibold text-gray-900 mb-2">
                    <i data-lucide="video" class="w-4 h-4 inline mr-2"></i>
                    Link học online
                </label>
                <input type="url" name="duong_dan" value="{{ old('duong_dan') }}" placeholder="https://meet.google.com/..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('duong_dan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <!-- Các buổi học trong tuần -->
            <div class="mb-6">
                <label class="block font-semibold text-gray-900 mb-3">
                    <i data-lucide="clock" class="w-4 h-4 inline mr-2"></i>
                    Lịch học trong tuần
                </label>
                
                <div id="buoi-hoc-container" class="space-y-3">
                    <!-- Buổi học mẫu -->
                    <div class="buoi-hoc-item flex gap-3 items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thứ</label>
                            <select name="buoi_hoc[0][thu]" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                <option value="1">Chủ nhật</option>
                                <option value="2" selected>Thứ 2</option>
                                <option value="3">Thứ 3</option>
                                <option value="4">Thứ 4</option>
                                <option value="5">Thứ 5</option>
                                <option value="6">Thứ 6</option>
                                <option value="7">Thứ 7</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu</label>
                            <input type="time" name="buoi_hoc[0][gio]" value="19:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        </div>
                        <button type="button" class="remove-buoi mt-7 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" style="display:none;">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <button type="button" id="add-buoi" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Thêm buổi học
                </button>
            </div>

            <!-- Nút submit -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('giasu.lophoc.index') }}" 
                   class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                    Hủy
                </a>
                @if($isPaid)
                    <!-- Đã thanh toán: Chỉ cần tạo lịch -->
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
                        <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                        Tạo lịch học
                    </button>
                @else
                    <!-- Chưa thanh toán: Thanh toán & Tạo lịch -->
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg">
                        <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                        Thanh toán & Tạo lịch
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();

    let buoiHocIndex = 1;

    document.getElementById('add-buoi').addEventListener('click', function() {
        const container = document.getElementById('buoi-hoc-container');
        const newBuoi = `
            <div class="buoi-hoc-item flex gap-3 items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thứ</label>
                    <select name="buoi_hoc[${buoiHocIndex}][thu]" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        <option value="1">Chủ nhật</option>
                        <option value="2">Thứ 2</option>
                        <option value="3">Thứ 3</option>
                        <option value="4">Thứ 4</option>
                        <option value="5" selected>Thứ 5</option>
                        <option value="6">Thứ 6</option>
                        <option value="7">Thứ 7</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu</label>
                    <input type="time" name="buoi_hoc[${buoiHocIndex}][gio]" value="19:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <button type="button" class="remove-buoi mt-7 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newBuoi);
        buoiHocIndex++;
        lucide.createIcons();
        updateRemoveButtons();
    });

    document.getElementById('buoi-hoc-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-buoi')) {
            e.target.closest('.buoi-hoc-item').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.buoi-hoc-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-buoi');
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
</script>
@endsection
