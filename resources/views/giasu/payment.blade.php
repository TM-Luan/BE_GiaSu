@extends('layouts.web')

@section('title', 'Thanh toán phí nhận lớp')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-6">
        <a href="{{ route('giasu.lophoc.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg mr-4">
                    <i data-lucide="credit-card" class="w-8 h-8"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Thanh toán phí nhận lớp</h1>
                    <p class="text-orange-100 mt-1">Hoàn tất thanh toán để bắt đầu dạy học</p>
                </div>
            </div>
        </div>

        <!-- Thông tin lớp học -->
        <div class="p-6 border-b border-gray-200 bg-blue-50">
            <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center">
                <i data-lucide="book-open" class="w-5 h-5 mr-2 text-blue-600"></i>
                Thông tin lớp học
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Môn học</p>
                    <p class="font-semibold text-gray-900">{{ $lopHoc->monHoc->TenMon ?? 'N/A' }} - {{ $lopHoc->khoiLop->BacHoc ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Học viên</p>
                    <p class="font-semibold text-gray-900">{{ $lopHoc->nguoiHoc->HoTen ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Học phí</p>
                    <p class="font-semibold text-blue-600">{{ number_format($lopHoc->HocPhi, 0, ',', '.') }} VNĐ/buổi</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Số buổi/tuần</p>
                    <p class="font-semibold text-gray-900">{{ $lopHoc->SoBuoiTuan ?? 2 }} buổi</p>
                </div>
            </div>
        </div>

        <!-- Chi tiết thanh toán -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center">
                <i data-lucide="calculator" class="w-5 h-5 mr-2 text-blue-600"></i>
                Chi tiết phí
            </h3>
            <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Học phí/buổi:</span>
                    <span class="font-medium">{{ number_format($lopHoc->HocPhi, 0, ',', '.') }} VNĐ</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Số buổi/tuần:</span>
                    <span class="font-medium">{{ $lopHoc->SoBuoiTuan ?? 2 }} buổi</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Số tuần (1 tháng):</span>
                    <span class="font-medium">4 tuần</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tỷ lệ phí:</span>
                    <span class="font-medium">30%</span>
                </div>
                <div class="border-t border-gray-300 pt-3 mt-3 flex justify-between">
                    <span class="font-bold text-gray-900">Tổng phí nhận lớp:</span>
                    <span class="font-bold text-2xl text-red-600">{{ number_format($phiNhanLop, 0, ',', '.') }} VNĐ</span>
                </div>
            </div>
        </div>

        <!-- Phương thức thanh toán -->
        <form action="{{ route('giasu.lophoc.payment.process', $lopHoc->LopYeuCauID) }}" method="POST" class="p-6">
            @csrf
            <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center">
                <i data-lucide="wallet" class="w-5 h-5 mr-2 text-blue-600"></i>
                Chọn phương thức thanh toán
            </h3>
            
            <div class="space-y-3">
                <!-- VNPAY -->
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                    <input type="radio" name="loai_giao_dich" value="VNPAY" class="w-5 h-5 text-blue-600" required>
                    <div class="ml-4 flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="credit-card" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <span class="ml-3 font-semibold text-gray-900">VNPAY</span>
                    </div>
                </label>

                <!-- MoMo -->
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-pink-500 hover:bg-pink-50 transition-all">
                    <input type="radio" name="loai_giao_dich" value="MoMo" class="w-5 h-5 text-pink-600">
                    <div class="ml-4 flex items-center">
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="smartphone" class="w-6 h-6 text-pink-600"></i>
                        </div>
                        <span class="ml-3 font-semibold text-gray-900">MoMo</span>
                    </div>
                </label>

                <!-- ZaloPay -->
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                    <input type="radio" name="loai_giao_dich" value="ZaloPay" class="w-5 h-5 text-blue-600">
                    <div class="ml-4 flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="smartphone" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <span class="ml-3 font-semibold text-gray-900">ZaloPay</span>
                    </div>
                </label>

                <!-- Chuyển khoản -->
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition-all">
                    <input type="radio" name="loai_giao_dich" value="ChuyenKhoan" class="w-5 h-5 text-green-600">
                    <div class="ml-4 flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="building" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <span class="ml-3 font-semibold text-gray-900">Chuyển khoản ngân hàng</span>
                    </div>
                </label>
            </div>

            @error('loai_giao_dich')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror

            <!-- Nút thanh toán -->
            <div class="mt-6 flex gap-3">
                <a href="{{ route('giasu.lophoc.index') }}" 
                   class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                    Hủy
                </a>
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                    Thanh toán {{ number_format($phiNhanLop, 0, ',', '.') }} VNĐ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
