<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocYeuCau;
use Illuminate\Support\Facades\Request;

class TestLopHocSearch extends Command
{
    protected $signature = 'test:lophoc-search';
    protected $description = 'Test tìm kiếm và lọc lớp học';

    public function handle()
    {
        $this->info('=== TEST TÌM KIẾM VÀ LỌC ===');
        
        // Test 1: Lọc theo trạng thái DangHoc
        $this->info("\n--- Test 1: Lọc TrangThai = 'DangHoc' ---");
        $result1 = LopHocYeuCau::where('TrangThai', 'DangHoc')->count();
        $this->line("Kết quả: {$result1} lớp");
        
        // Test 2: Lọc theo hình thức Online
        $this->info("\n--- Test 2: Lọc HinhThuc = 'Online' ---");
        $result2 = LopHocYeuCau::where('HinhThuc', 'Online')->count();
        $this->line("Kết quả: {$result2} lớp");
        
        // Test 3: Tìm kiếm "Toán"
        $this->info("\n--- Test 3: Tìm kiếm 'Toán' ---");
        $search = 'Toán';
        $result3 = LopHocYeuCau::where(function($q) use ($search) {
            $q->where('MoTa', 'like', "%$search%")
              ->orWhereHas('nguoiHoc', function($q) use ($search) {
                  $q->where('HoTen', 'like', "%$search%");
              })
              ->orWhereHas('giaSu', function($q) use ($search) {
                  $q->where('HoTen', 'like', "%$search%");
              })
              ->orWhereHas('monHoc', function($q) use ($search) {
                  $q->where('TenMon', 'like', "%$search%");
              });
        })->count();
        $this->line("Kết quả: {$result3} lớp");
        
        // Test 4: Tìm kiếm "Lê"
        $this->info("\n--- Test 4: Tìm kiếm 'Lê' (tên người học) ---");
        $search = 'Lê';
        $result4 = LopHocYeuCau::where(function($q) use ($search) {
            $q->where('MoTa', 'like', "%$search%")
              ->orWhereHas('nguoiHoc', function($q) use ($search) {
                  $q->where('HoTen', 'like', "%$search%");
              })
              ->orWhereHas('giaSu', function($q) use ($search) {
                  $q->where('HoTen', 'like', "%$search%");
              })
              ->orWhereHas('monHoc', function($q) use ($search) {
                  $q->where('TenMon', 'like', "%$search%");
              });
        })->count();
        $this->line("Kết quả: {$result4} lớp");
        
        // Test 5: Lọc kết hợp
        $this->info("\n--- Test 5: Lọc DangHoc + Online ---");
        $result5 = LopHocYeuCau::where('TrangThai', 'DangHoc')
            ->where('HinhThuc', 'Online')
            ->count();
        $this->line("Kết quả: {$result5} lớp");
        
        // Test 6: Tìm kiếm + Lọc kết hợp
        $this->info("\n--- Test 6: Tìm 'Toán' + TrangThai='DangHoc' ---");
        $search = 'Toán';
        $result6 = LopHocYeuCau::where('TrangThai', 'DangHoc')
            ->where(function($q) use ($search) {
                $q->where('MoTa', 'like', "%$search%")
                  ->orWhereHas('nguoiHoc', function($q) use ($search) {
                      $q->where('HoTen', 'like', "%$search%");
                  })
                  ->orWhereHas('giaSu', function($q) use ($search) {
                      $q->where('HoTen', 'like', "%$search%");
                  })
                  ->orWhereHas('monHoc', function($q) use ($search) {
                      $q->where('TenMon', 'like', "%$search%");
                  });
            })->count();
        $this->line("Kết quả: {$result6} lớp");
        
        $this->info("\n✅ Tất cả test hoàn tất!");
        return 0;
    }
}
