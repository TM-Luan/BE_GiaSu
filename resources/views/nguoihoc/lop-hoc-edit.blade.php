@extends('layouts.web')

@section('title', 'Sửa lớp học')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('nguoihoc.lophoc.index') }}" class="text-gray-500 hover:text-blue-600 flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Sửa thông tin lớp học</h1>

        <form action="{{ route('nguoihoc.lophoc.update', $lopHoc->LopYeuCauID) }}" method="POST">
            @csrf
            @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Môn học</label>
                    <select name="MonID" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($monHocList as $mon)
                            <option value="{{ $mon->MonID }}" {{ $lopHoc->MonID == $mon->MonID ? 'selected' : '' }}>
                                {{ $mon->TenMon }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lớp</label>
                    <select name="KhoiLopID" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($khoiLopList as $khoi)
                            <option value="{{ $khoi->KhoiLopID }}" {{ $lopHoc->KhoiLopID == $khoi->KhoiLopID ? 'selected' : '' }}>
                                {{ $khoi->BacHoc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình thức học</label>
                    <select name="HinhThuc" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Offline" {{ $lopHoc->HinhThuc == 'Offline' ? 'selected' : '' }}>Tại nhà (Offline)</option>
                        <option value="Online" {{ $lopHoc->HinhThuc == 'Online' ? 'selected' : '' }}>Trực tuyến (Online)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yêu cầu gia sư</label>
                    <select name="DoiTuongID" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($doiTuongList as $dt)
                            <option value="{{ $dt->DoiTuongID }}" {{ $lopHoc->DoiTuongID == $dt->DoiTuongID ? 'selected' : '' }}>
                                {{ $dt->TenDoiTuong }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Học phí (VNĐ/buổi)</label>
                    <input type="number" name="HocPhi" value="{{ $lopHoc->HocPhi }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thời lượng (phút/buổi)</label>
                    <input type="number" name="ThoiLuong" value="{{ $lopHoc->ThoiLuong }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số buổi / tuần</label>
                    <input type="number" name="SoBuoiTuan" value="{{ $lopHoc->SoBuoiTuan }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lịch học mong muốn</label>
                    <input type="text" name="LichHocMongMuon" value="{{ $lopHoc->LichHocMongMuon }}" placeholder="Ví dụ: Tối thứ 2, 4, 6" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả chi tiết / Yêu cầu khác</label>
                <textarea name="MoTa" rows="4" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ $lopHoc->MoTa }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('nguoihoc.lophoc.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection