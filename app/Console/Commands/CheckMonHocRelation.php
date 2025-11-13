<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocYeuCau;
use App\Models\MonHoc;
use Illuminate\Support\Facades\DB;

class CheckMonHocRelation extends Command
{
    protected $signature = 'check:monhoc';
    protected $description = 'Kiểm tra relationship MonHoc trong LopHocYeuCau';

    public function handle()
    {
        $this->info('Kiểm tra dữ liệu MonHoc...');
        $this->newLine();
        
        // 1. Tổng số lớp học
        $totalLops = LopHocYeuCau::count();
        $this->info("📊 Tổng số lớp học: {$totalLops}");
        
        // 2. Số lớp không có MonID
        $withoutMonID = LopHocYeuCau::whereNull('MonID')->count();
        $this->info("❌ Lớp không có MonID: {$withoutMonID}");
        
        // 3. Số lớp có MonID nhưng không tìm thấy trong bảng MonHoc
        $invalidMonID = DB::table('LopHocYeuCau as l')
            ->leftJoin('MonHoc as m', 'l.MonID', '=', 'm.MonID')
            ->whereNotNull('l.MonID')
            ->whereNull('m.MonID')
            ->count();
        $this->warn("⚠️  Lớp có MonID nhưng MonHoc không tồn tại: {$invalidMonID}");
        
        // 4. Số lớp hợp lệ
        $validLops = $totalLops - $withoutMonID - $invalidMonID;
        $this->info("✓ Lớp hợp lệ: {$validLops}");
        
        $this->newLine();
        
        // 5. Hiển thị 10 lớp đầu tiên
        $this->info('10 Lớp học đầu tiên:');
        $this->newLine();
        
        $lops = LopHocYeuCau::with('monHoc')->limit(10)->get();
        
        $headers = ['LopID', 'MonID', 'TenMonHoc', 'Status'];
        $rows = [];
        
        foreach ($lops as $lop) {
            $rows[] = [
                $lop->LopYeuCauID,
                $lop->MonID ?? 'NULL',
                $lop->monHoc ? $lop->monHoc->TenMon : 'NOT FOUND',
                $lop->monHoc ? '✓' : '✗'
            ];
        }
        
        $this->table($headers, $rows);
        
        // 6. List danh sách MonID invalid
        if ($invalidMonID > 0) {
            $this->newLine();
            $this->warn('Các MonID không hợp lệ:');
            
            $invalidLops = DB::table('LopHocYeuCau as l')
                ->leftJoin('MonHoc as m', 'l.MonID', '=', 'm.MonID')
                ->whereNotNull('l.MonID')
                ->whereNull('m.MonID')
                ->select('l.LopYeuCauID', 'l.MonID')
                ->limit(20)
                ->get();
            
            foreach ($invalidLops as $lop) {
                $this->line("  LopID: {$lop->LopYeuCauID} -> MonID: {$lop->MonID} (không tồn tại)");
            }
            
            $this->newLine();
            $this->info('💡 Gợi ý: Chạy lệnh "php artisan fix:invalid-monhoc" để sửa');
        }
        
        return 0;
    }
}
