<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocYeuCau;
use App\Models\MonHoc;

class FixMonHocData extends Command
{
    protected $signature = 'fix:monhoc';
    protected $description = 'Fix MonID cho các LopHocYeuCau không có môn học';

    public function handle()
    {
        $this->info('Bắt đầu fix dữ liệu MonID...');
        
        // Lấy tất cả lớp học không có MonID
        $lopsWithoutMonID = LopHocYeuCau::whereNull('MonID')->get();
        
        if ($lopsWithoutMonID->isEmpty()) {
            $this->info('✓ Tất cả lớp học đã có MonID!');
            return 0;
        }
        
        $this->warn("Tìm thấy {$lopsWithoutMonID->count()} lớp học không có MonID");
        
        // Lấy một môn học mặc định (môn đầu tiên)
        $defaultMonHoc = MonHoc::first();
        
        if (!$defaultMonHoc) {
            $this->error('Không tìm thấy môn học nào trong database!');
            $this->info('Vui lòng thêm môn học trước.');
            return 1;
        }
        
        $this->info("Sử dụng môn học mặc định: {$defaultMonHoc->TenMonHoc} (ID: {$defaultMonHoc->MonID})");
        
        if (!$this->confirm('Bạn có muốn gán môn học này cho tất cả lớp không có MonID?')) {
            $this->info('Đã hủy.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar($lopsWithoutMonID->count());
        $bar->start();
        
        $updated = 0;
        foreach ($lopsWithoutMonID as $lop) {
            $lop->update(['MonID' => $defaultMonHoc->MonID]);
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Đã cập nhật {$updated} lớp học!");
        
        return 0;
    }
}
