<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocYeuCau;

class TestLopHocFilter extends Command
{
    protected $signature = 'test:lophoc-filter';
    protected $description = 'Test lọc và tìm kiếm lớp học';

    public function handle()
    {
        $this->info('=== TEST DỮ LIỆU LỚP HỌC ===');
        
        // Tổng số lớp học
        $total = LopHocYeuCau::count();
        $this->info("Tổng số lớp học: {$total}");
        
        // Kiểm tra trạng thái
        $this->info("\n--- Thống kê theo Trạng Thái ---");
        $trangThaiList = LopHocYeuCau::select('TrangThai')
            ->distinct()
            ->pluck('TrangThai');
        
        foreach ($trangThaiList as $tt) {
            $count = LopHocYeuCau::where('TrangThai', $tt)->count();
            $this->line("TrangThai = '{$tt}': {$count} lớp");
        }
        
        // Kiểm tra hình thức
        $this->info("\n--- Thống kê theo Hình Thức ---");
        $hinhThucList = LopHocYeuCau::select('HinhThuc')
            ->distinct()
            ->pluck('HinhThuc');
        
        foreach ($hinhThucList as $ht) {
            $count = LopHocYeuCau::where('HinhThuc', $ht)->count();
            $this->line("HinhThuc = '{$ht}': {$count} lớp");
        }
        
        // Test tìm kiếm
        $this->info("\n--- Test Tìm Kiếm ---");
        
        // Tìm theo người học
        $search1 = LopHocYeuCau::whereHas('nguoiHoc', function($q) {
            $q->where('HoTen', 'like', '%sinh%');
        })->count();
        $this->line("Tìm 'sinh' trong người học: {$search1} kết quả");
        
        // Tìm theo môn học
        $search2 = LopHocYeuCau::whereHas('monHoc', function($q) {
            $q->where('TenMon', 'like', '%Toán%');
        })->count();
        $this->line("Tìm 'Toán' trong môn học: {$search2} kết quả");
        
        // Hiển thị 3 lớp đầu với đầy đủ thông tin
        $this->info("\n--- 3 Lớp Học Mẫu ---");
        $samples = LopHocYeuCau::with(['nguoiHoc', 'monHoc'])
            ->take(3)
            ->get();
        
        foreach ($samples as $lop) {
            $this->line("ID: {$lop->LopYeuCauID}");
            $this->line("  Người học: " . ($lop->nguoiHoc->HoTen ?? 'N/A'));
            $this->line("  Môn học: " . ($lop->monHoc->TenMon ?? 'N/A'));
            $this->line("  Trạng thái: {$lop->TrangThai}");
            $this->line("  Hình thức: {$lop->HinhThuc}");
            $this->line("---");
        }
        
        $this->info("\n✅ Test hoàn tất!");
        return 0;
    }
}
