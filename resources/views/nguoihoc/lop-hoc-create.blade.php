@extends('layouts.web')

@section('title', 'Tạo lớp học mới')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tạo lớp học mới</h1>
            <p class="text-gray-500 mt-2 text-base font-medium">Cung cấp thông tin chi tiết để tìm gia sư phù hợp nhất.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('nguoihoc.lophoc.store') }}" method="POST"
            class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
            @csrf

            <!-- Hiển thị lỗi validation -->
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4" role="alert">
                    <p class="font-bold text-red-800">Vui lòng sửa các lỗi sau:</p>
                    <ul class="list-disc list-inside text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Hàng 1: Môn học, Khối lớp, Đối tượng -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="MonID" class="block text-sm font-medium text-gray-700 mb-1">Môn học</label>
                    <select id="MonID" name="MonID"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn môn --</option>
                        @foreach($monHocList as $mon)
                            <option value="{{ $mon->MonID }}" {{ old('MonID') == $mon->MonID ? 'selected' : '' }}>
                                {{ $mon->TenMon }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="KhoiLopID" class="block text-sm font-medium text-gray-700 mb-1">Khối lớp</label>
                    <select id="KhoiLopID" name="KhoiLopID"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn khối lớp --</option>
                        @foreach($khoiLopList as $khoi)
                            <option value="{{ $khoi->KhoiLopID }}" {{ old('KhoiLopID') == $khoi->KhoiLopID ? 'selected' : '' }}>
                                {{ $khoi->BacHoc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="DoiTuongID" class="block text-sm font-medium text-gray-700 mb-1">Đối tượng học</label>
                    <select id="DoiTuongID" name="DoiTuongID"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn đối tượng --</option>
                        @foreach($doiTuongList as $dt)
                            <option value="{{ $dt->DoiTuongID }}" {{ old('DoiTuongID') == $dt->DoiTuongID ? 'selected' : '' }}>
                                {{ $dt->TenDoiTuong }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Hàng 2: Hình thức, Học phí, Thời lượng -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="HinhThuc" class="block text-sm font-medium text-gray-700 mb-1">Hình thức học</label>
                    <select id="HinhThuc" name="HinhThuc"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="Offline" {{ old('HinhThuc') == 'Offline' ? 'selected' : '' }}>Offline (Tại nhà)
                        </option>
                        <option value="Online" {{ old('HinhThuc') == 'Online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <div>
                    <label for="HocPhi" class="block text-sm font-medium text-gray-700 mb-1">Học phí (VND/buổi)</label>
                    <input type="number" id="HocPhi" name="HocPhi" value="{{ old('HocPhi') }}" placeholder="VD: 200000"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="ThoiLuong" class="block text-sm font-medium text-gray-700 mb-1">Thời lượng
                        (phút/buổi)</label>
                    <select id="ThoiLuong" name="ThoiLuong"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="60" {{ old('ThoiLuong') == 60 ? 'selected' : '' }}>60 phút</option>
                        <option value="90" {{ old('ThoiLuong', 90) == 90 ? 'selected' : '' }}>90 phút</option>
                        <option value="120" {{ old('ThoiLuong') == 120 ? 'selected' : '' }}>120 phút</option>
                    </select>
                </div>
               
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="SoBuoiTuan" class="block text-sm font-medium text-gray-700 mb-1">Số buổi / tuần</label>
                    <select id="SoBuoiTuan" name="SoBuoiTuan"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('SoBuoiTuan', 2) == $i ? 'selected' : '' }}>
                                {{ $i }} buổi/tuần
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="LichHocMongMuon" class="block text-sm font-medium text-gray-700 mb-1">Lịch học mong
                        muốn</label>
                    <input type="text" id="LichHocMongMuon" name="LichHocMongMuon" value="{{ old('LichHocMongMuon') }}"
                        placeholder="VD: Tối T3, Tối T5"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Hàng 4: Mô tả thêm -->
            <div>
                <label for="MoTa" class="block text-sm font-medium text-gray-700 mb-1">Mô tả thêm (Không bắt buộc)</label>
                <textarea id="MoTa" name="MoTa" rows="4"
                    placeholder="Nhập thêm yêu cầu về gia sư, tình trạng học tập của học viên..."
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('MoTa') }}</textarea>
            </div>

            <!-- Nút Submit -->
            <div class="text-right">
                <a href="{{ route('nguoihoc.lophoc.index') }}"
                    class="mr-2 inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none">
                    Hủy bỏ
                </a>
                <button type="submit"
                    class="inline-flex justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Đăng tin tìm gia sư
                </button>
            </div>

        </form>
    </div>
@endsection