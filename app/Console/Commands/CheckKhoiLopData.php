<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocYeuCau;
use App\Models\KhoiLop;
use Illuminate\Support\Facades\DB;

class CheckKhoiLopData extends Command
{
    protected $signature = 'check:khoilop';
    protected $description = 'Kiểm tra dữ liệu KhoiLop trong LopHocYeuCau';

    public function handle()
    {
        $this->info('Kiểm tra dữ liệu KhoiLop...');
        $this->newLine();
        
        // 1. Tổng số lớp học
        $totalLops = LopHocYeuCau::count();
        $this->info("📊 Tổng số lớp học: {$totalLops}");
        
        // 2. Số lớp không có KhoiLopID
        $withoutKhoiLopID = LopHocYeuCau::whereNull('KhoiLopID')->count();
        $this->warn("❌ Lớp không có KhoiLopID: {$withoutKhoiLopID}");
        
        // 3. Số lớp có KhoiLopID nhưng không tìm thấy trong bảng KhoiLop
        $invalidKhoiLopID = DB::table('LopHocYeuCau as l')
            ->leftJoin('KhoiLop as k', 'l.KhoiLopID', '=', 'k.KhoiLopID')
            ->whereNotNull('l.KhoiLopID')
            ->whereNull('k.KhoiLopID')
            ->count();
        $this->warn("⚠️  Lớp có KhoiLopID nhưng KhoiLop không tồn tại: {$invalidKhoiLopID}");
        
        // 4. Số lớp hợp lệ
        $validLops = $totalLops - $withoutKhoiLopID - $invalidKhoiLopID;
        $this->info("✓ Lớp hợp lệ: {$validLops}");
        
        $this->newLine();
        
        // 5. Hiển thị 10 lớp đầu tiên
        $this->info('10 Lớp học đầu tiên:');
        $this->newLine();
        
        $lops = LopHocYeuCau::with(['monHoc', 'khoiLop'])->limit(10)->get();
        
        $headers = ['LopID', 'MonID', 'TenMon', 'KhoiLopID', 'TenKhoi'];
        $rows = [];
        
        foreach ($lops as $lop) {
            $rows[] = [
                $lop->LopYeuCauID,
                $lop->MonID ?? 'NULL',
                $lop->monHoc ? $lop->monHoc->TenMon : 'N/A',
                $lop->KhoiLopID ?? 'NULL',
                $lop->khoiLop ? $lop->khoiLop->BacHoc : 'N/A'
            ];
        }
        
        $this->table($headers, $rows);
        
        // 6. Danh sách KhoiLop có sẵn
        $this->newLine();
        $this->info('Danh sách Khối lớp có sẵn:');
        $khoiLops = KhoiLop::all();
        foreach ($khoiLops as $khoi) {
            $this->line("  ID: {$khoi->KhoiLopID} - {$khoi->BacHoc}");
        }
        
        return 0;
    }
}
