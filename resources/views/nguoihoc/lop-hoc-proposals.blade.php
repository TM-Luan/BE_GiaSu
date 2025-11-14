@extends('layouts.web')

@section('title', 'Danh sách đề nghị')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('nguoihoc.lophoc.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Quay lại danh sách lớp
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Đề nghị dạy: {{ $lopHoc->monHoc->TenMon }} {{ $lopHoc->khoiLop->BacHoc }}
            </h1>
            <p class="text-gray-500 mt-1">
                Học phí: <span class="font-semibold text-green-600">{{ number_format($lopHoc->HocPhi) }} đ/buổi</span> 
                • Hình thức: {{ $lopHoc->HinhThuc }}
            </p>
        </div>
        <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg font-semibold text-sm">
            {{ $proposals->count() }} lượt ứng tuyển
        </div>
    </div>

    <div class="space-y-4">
        @forelse($proposals as $item)
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-6 items-start md:items-center hover:border-blue-200 transition-all">
                
                <div class="flex-shrink-0">
                    <img src="{{ $item->giaSu->AnhDaiDien ? asset($item->giaSu->AnhDaiDien) : 'https://ui-avatars.com/api/?name=' . urlencode($item->giaSu->HoTen) . '&background=random' }}" 
                         alt="{{ $item->giaSu->HoTen }}" 
                         class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                </div>

                <div class="flex-grow">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $item->giaSu->HoTen }}</h3>
                        <a href="{{ route('nguoihoc.giasu.show', $item->giaSu->GiaSuID) }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                            (Xem hồ sơ)
                        </a>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="mr-3"><i data-lucide="graduation-cap" class="w-4 h-4 inline mr-1"></i> {{ $item->giaSu->TruongDaoTao ?? 'Chưa cập nhật' }}</span>
                        <span><i data-lucide="star" class="w-4 h-4 inline mr-1 text-yellow-500"></i> {{ number_format($item->giaSu->danhGia->avg('DiemSo'), 1) }}</span>
                    </div>

                    @if($item->GhiChu)
                        <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700 italic border border-gray-100">
                            "{{ $item->GhiChu }}"
                        </div>
                    @endif
                    
                    <div class="text-xs text-gray-400 mt-2">
                        Ứng tuyển: {{ $item->NgayTao ? \Carbon\Carbon::parse($item->NgayTao)->format('d/m/Y H:i') : '' }}
                    </div>
                </div>

                <div class="flex-shrink-0 flex flex-col gap-2 min-w-[140px]">
                    @if($item->TrangThai == 'Pending')
                        <form action="{{ route('nguoihoc.proposals.accept', $item->YeuCauID) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors" onclick="return confirm('Bạn có chắc chắn muốn chọn gia sư này? Các ứng viên khác sẽ bị từ chối.')">
                                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                                Chấp nhận
                            </button>
                        </form>

                        <form action="{{ route('nguoihoc.proposals.reject', $item->YeuCauID) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white text-red-600 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-50 transition-colors">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Từ chối
                            </button>
                        </form>
                    
                    @elseif($item->TrangThai == 'Accepted')
                        <span class="inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-lg">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Đã chọn
                        </span>
                    
                    @elseif($item->TrangThai == 'Rejected')
                        <span class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg">
                            Đã từ chối
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Chưa có đề nghị nào</h3>
                <p class="text-gray-500 text-sm">Hiện tại chưa có gia sư nào ứng tuyển vào lớp học này.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection