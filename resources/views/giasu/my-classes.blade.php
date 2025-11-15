@extends('layouts.web')

@section('title', 'Lớp học của tôi')

@section('content')
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Lớp học của tôi</h1>
        <p class="text-gray-500 mt-2 text-base font-medium">Quản lý các lớp học bạn đang dạy</p>
    </div>

    {{-- Pending Proposals --}}
    @if($pendingProposals->count() > 0)
        <div class="mb-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-yellow-900 mb-4 flex items-center">
                <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
                Đề nghị đang chờ duyệt ({{ $pendingProposals->count() }})
            </h2>
            <div class="space-y-3">
                @foreach($pendingProposals as $proposal)
                    @php
                        $lop = $proposal->lopHocYeuCau;
                        $monHoc = $lop->monHoc->TenMon ?? 'N/A';
                        $khoiLop = $lop->khoiLop->BacHoc ?? '';
                    @endphp
                    <div class="bg-white rounded-lg p-4 flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $monHoc }} @if($khoiLop) - {{ $khoiLop }} @endif</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>
                                {{ $lop->nguoiHoc->taiKhoan->HoTen ?? 'Học sinh' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Gửi lúc: {{ \Carbon\Carbon::parse($proposal->NgayTao)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                            Chờ duyệt
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Active Classes --}}
    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
        Lớp đang dạy
        @if($lopHocList->total() > 0)
            <span class="ml-3 bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $lopHocList->total() }}</span>
        @endif
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($lopHocList as $lop)
            @php
                $monHoc = $lop->monHoc->TenMon ?? 'Chưa rõ môn học';
                $khoiLop = $lop->khoiLop->BacHoc ?? '';
                $tenLop = $monHoc . ($khoiLop ? " - {$khoiLop}" : '');
                $hocPhi = $lop->HocPhi ?? 0;
                $hienThiHocPhi = $hocPhi > 0 ? number_format($hocPhi, 0, ',', '.') . 'đ' : 'Liên hệ';
                $nguoiHoc = $lop->nguoiHoc->taiKhoan->HoTen ?? 'Học sinh';
                
                $statusClass = match($lop->TrangThai) {
                    'DangHoc' => 'bg-green-100 text-green-700',
                    'HoanThanh' => 'bg-gray-100 text-gray-700',
                    default => 'bg-blue-100 text-blue-700'
                };
                
                $statusText = match($lop->TrangThai) {
                    'DangHoc' => 'Đang dạy',
                    'HoanThanh' => 'Hoàn thành',
                    default => $lop->TrangThai
                };
            @endphp
            
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:border-green-300 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="font-bold text-lg text-gray-900 flex-1">{{ $tenLop }}</h3>
                    <span class="{{ $statusClass }} px-2.5 py-1 rounded-full text-xs font-bold">
                        {{ $statusText }}
                    </span>
                </div>

                @if($lop->MoTa)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $lop->MoTa }}</p>
                @endif

                <div class="bg-gray-50 rounded-xl p-3 mb-4 space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                        <span>{{ $nguoiHoc }}</span>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600">
                        <i data-lucide="banknote" class="w-4 h-4 mr-2 text-gray-400"></i>
                        <span class="font-semibold text-green-600">{{ $hienThiHocPhi }}/buổi</span>
                    </div>

                    @if($lop->HinhThuc)
                        <div class="flex items-center text-sm text-gray-600">
                            <i data-lucide="monitor" class="w-4 h-4 mr-2 text-gray-400"></i>
                            <span>{{ $lop->HinhThuc }}</span>
                        </div>
                    @endif
                </div>

                <a href="{{ route('giasu.lophoc.show', $lop->LopYeuCauID) }}" 
                   class="block text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Xem chi tiết
                </a>
            </div>
        @empty
            <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-500">
                <div class="bg-gray-100 p-4 rounded-full mb-4">
                    <i data-lucide="book-open" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-lg font-medium">Bạn chưa có lớp học nào</p>
                <a href="{{ route('giasu.dashboard') }}" class="text-green-600 hover:underline mt-2 font-medium">
                    Tìm lớp học ngay
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($lopHocList->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $lopHocList->links() }}
        </div>
    @endif

@endsection
