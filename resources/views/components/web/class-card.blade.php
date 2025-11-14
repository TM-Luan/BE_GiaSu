@props(['lopHoc'])

@php
    // Logic xử lý Trạng thái để hiển thị Tag
    $statusText = '';
    $statusColorClass = '';
    switch ($lopHoc->TrangThai) {
        case 'TimGiaSu':
            $statusText = 'Đang tìm gia sư';
            // Màu Vàng (Yellow)
            $statusColorClass = 'bg-yellow-50 text-yellow-800 border-yellow-200';
            break;
        case 'DangHoc':
            $statusText = 'Đã có gia sư';
            // Màu Xanh lá (Green)
            $statusColorClass = 'bg-green-50 text-green-800 border-green-200';
            break;
        case 'HoanThanh':
            $statusText = 'Đã hoàn thành';
            // Màu Xám (Gray)
            $statusColorClass = 'bg-gray-100 text-gray-700 border-gray-200';
            break;
        case 'Huy':
            $statusText = 'Đã hủy';
            // Màu Đỏ (Red)
            $statusColorClass = 'bg-red-50 text-red-800 border-red-200';
            break;
        default:
            $statusText = 'Không xác định';
            $statusColorClass = 'bg-gray-100 text-gray-700 border-gray-200';
    }

    // Lấy tên môn học và khối lớp
    $tenMonHoc = $lopHoc->monHoc->TenMon ?? 'Môn học';
    $tenKhoiLop = $lopHoc->khoiLop->BacHoc ?? '';
    
    // Tạo tiêu đề
    $tieuDe = "Tìm gia sư $tenMonHoc $tenKhoiLop";
    if ($lopHoc->TrangThai == 'DangHoc' && $lopHoc->giaSu) {
        $tieuDe = "Lớp $tenMonHoc (GS: " . $lopHoc->giaSu->HoTen . ")";
    } elseif ($lopHoc->TrangThai == 'HoanThanh') {
        $tieuDe = "Lớp $tenMonHoc (Đã kết thúc)";
    }
@endphp

<!-- THIẾT KẾ CARD MỚI (ĐẸP HƠN) -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:border-blue-200 transition-all duration-300 flex flex-col group overflow-hidden hover:shadow-xl">
    
    <!-- Phần 1: Ảnh Header -->
    <a href="{{ route('nguoihoc.lophoc.show', $lopHoc->LopYeuCauID) }}" class="h-44 w-full overflow-hidden relative block group">
    
        <img src="https://placehold.co/600x400/E0E7FF/3B82F6?text={{ urlencode($tenMonHoc) }}&font=roboto" 
            alt="{{ $tenMonHoc }}" 
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        
        <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold rounded-full border {{ $statusColorClass }} bg-white/80 backdrop-blur-sm shadow-sm">
            {{ $statusText }}
        </span>

    </a>

    <!-- Phần 2: Nội dung thông tin -->
    <div class="p-6 flex flex-col flex-1">
        <!-- Tiêu đề lớp học -->
        <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors">
            {{ $tieuDe }}
        </h3>

        <!-- Danh sách chi tiết (Căn chỉnh đẹp hơn) -->
        <div class="space-y-3 text-sm mb-6">
            <div class="flex items-center text-gray-600">
                <i data-lucide="monitor" class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0"></i>
                <span>Hình thức: <span class="font-semibold text-gray-900">{{ $lopHoc->HinhThuc }}</span></span>
            </div>
            <div class="flex items-center text-gray-600">
                <i data-lucide="calendar" class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0"></i>
                <span>Lịch học: <span class="font-semibold text-gray-900">{{ $lopHoc->LichHocMongMuon }} ({{ $lopHoc->SoBuoiTuan }} buổi/tuần)</span></span>
            </div>
            <div class="flex items-center text-gray-600">
                <i data-lucide="banknote" class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0"></i>
                <span>Học phí: <span class="font-bold text-green-600 text-base">{{ number_format($lopHoc->HocPhi) }} VND/buổi</span></span>
            </div>
        </div>

        <!-- [BẮT ĐẦU THAY THẾ] Nút hành động (Logic mới cho Mobile) -->
        <div class="mt-auto pt-4 border-t border-gray-100">
            
            @if ($lopHoc->TrangThai == 'TimGiaSu')
                <div class="flex items-center gap-2">
                    <a href="{{ route('nguoihoc.lophoc.proposals', $lopHoc->LopYeuCauID) }}" 
                       class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors flex-1 relative">
                        <i data-lucide="users" class="w-4 h-4 mr-1.5"></i>
                        Xem đề nghị
                        
                        {{-- Hiển thị badge số lượng nếu cần (Logic nâng cao) --}}
                        @php
                            $countPending = $lopHoc->yeuCauNhanLops->where('VaiTroNguoiGui', 'GiaSu')->where('TrangThai', 'Pending')->count();
                        @endphp
                        @if($countPending > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white">
                                {{ $countPending }}
                            </span>
                        @endif
                    </a>
                    <!-- Nút phụ: Sửa -->
                    <a href="{{ route('nguoihoc.lophoc.edit', $lopHoc->LopYeuCauID) }}" 
                    class="inline-flex items-center justify-center p-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" 
                    title="Sửa thông tin lớp">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                    <!-- Nút phụ: Đóng -->
                    <form action="{{ route('nguoihoc.lophoc.cancel', $lopHoc->LopYeuCauID) }}" 
                        method="POST" 
                        class="flex" 
                        onsubmit="return confirm('Bạn có chắc chắn muốn đóng lớp học này không? Hành động này sẽ hủy tất cả các lời mời hiện tại.');">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center justify-center p-2 text-sm font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" 
                                title="Đóng tin">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

            @elseif ($lopHoc->TrangThai == 'DangHoc')
                <!-- Trạng thái Đang học: Xem lịch, Khiếu nại -->
                <div class="flex items-center gap-2">
                    <!-- Nút chính: Xem lịch -->
                    <a href="{{ route('nguoihoc.lophoc.schedule', $lopHoc->LopYeuCauID) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors flex-1">
                        <i data-lucide="calendar" class="w-4 h-4 mr-1.5"></i>
                        Xem lịch
                    </a>
                    <!-- Nút phụ: Khiếu nại -->
                    <a href="{{ route('nguoihoc.lophoc.complaint.create', $lopHoc->LopYeuCauID) }}" 
                       class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-1.5"></i>
                        Khiếu nại
                    </a>
                </div>

            @elseif ($lopHoc->TrangThai == 'HoanThanh')
                <!-- Trạng thái Hoàn thành -->
                <a href="#" class="inline-flex items-center font-semibold text-green-600 hover:text-green-800 transition-colors text-sm">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>
                    Đã hoàn thành
                </a>
            @elseif ($lopHoc->TrangThai == 'Huy')
                <div class="flex items-center justify-between w-full">
                    
                    <span class="text-gray-400 text-sm italic">Lớp đã bị hủy</span>

                    <form action="{{ route('nguoihoc.lophoc.destroy', $lopHoc->LopYeuCauID) }}" 
                          method="POST" 
                          onsubmit="return confirm('Bạn chắc chắn muốn xóa vĩnh viễn lớp này khỏi lịch sử không?');">
                        @csrf
                        @method('DELETE')
                        
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:text-red-700 transition-colors shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-1.5"></i>
                            Xóa lớp
                        </button>
                    </form>
                </div>
            

            @else
                <!-- Trạng thái Hủy hoặc khác -->
                <a href="#" class="inline-flex items-center font-semibold text-gray-500 hover:text-gray-700 transition-colors text-sm">
                    <i data-lucide="info" class="w-4 h-4 mr-1.5"></i>
                    Xem chi tiết
                </a>
            @endif

        </div>
        <!-- [KẾT THÚC THAY THẾ] -->

    </div>
</div>