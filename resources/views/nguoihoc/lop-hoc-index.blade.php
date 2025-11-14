@extends('layouts.web')

@section('title', 'Lớp học của tôi')

@section('content')
    <div class="max-w-6xl mx-auto">
        
        <!-- Header: Tiêu đề và Nút "Tạo lớp học" -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Lớp học của tôi</h1>
                <p class="text-gray-500 mt-2 text-base font-medium">Quản lý tất cả các lớp học bạn đã đăng hoặc đang theo học.</p>
            </div>
            <a href="{{ route('nguoihoc.lophoc.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 whitespace-nowrap">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Tạo lớp học
            </a>
        </div>

        <!-- Thanh Tìm kiếm và Bộ lọc -->
        <form method="GET" action="{{ route('nguoihoc.lophoc.index') }}" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="relative flex-grow">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm theo tên lớp, môn học..."
                           class="w-full px-4 py-3 pl-12 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                </div>
                <!-- Submit Search (ẩn) -->
                <button type="submit" class="hidden">Tìm</button>

                <!-- Filter Buttons -->
                <div class="flex-shrink-0 flex gap-2">
                    <a href="{{ route('nguoihoc.lophoc.index') }}" class="px-4 py-3 rounded-xl font-medium border transition-colors {{ !request('trangthai') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                        Tất cả
                    </a>
                    <a href="{{ route('nguoihoc.lophoc.index', ['trangthai' => 'TimGiaSu']) }}" class="px-4 py-3 rounded-xl font-medium border transition-colors {{ request('trangthai') == 'TimGiaSu' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                        Đang tìm gia sư
                    </a>
                    <a href="{{ route('nguoihoc.lophoc.index', ['trangthai' => 'DangHoc']) }}" class="px-4 py-3 rounded-xl font-medium border transition-colors {{ request('trangthai') == 'DangHoc' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                        Đã có gia sư
                    </a>
                    <a href="{{ route('nguoihoc.lophoc.index', ['trangthai' => 'HoanThanh']) }}" class="px-4 py-3 rounded-xl font-medium border transition-colors {{ request('trangthai') == 'HoanThanh' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                        Đã hoàn thành
                    </a>
                </div>
            </div>
        </form>

        <!-- Grid Danh sách lớp học -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($lopHocList as $lopHoc)
                <!-- Gọi component class-card -->
                <x-web.class-card :lopHoc="$lopHoc" />
            @empty
                <div class="col-span-2 flex flex-col items-center justify-center py-16 text-gray-500 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-lg font-medium">Bạn chưa tạo lớp học nào.</p>
                    <p class="text-sm mb-4">Hãy bắt đầu tạo lớp học mới để tìm gia sư nhé!</p>
                    <a href="{{ route('nguoihoc.lophoc.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Tạo lớp học ngay
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Phân trang -->
        <div class="mt-12">
            {{ $lopHocList->links() }}
        </div>
    </div>
@endsection