<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaiKhoan;
use App\Models\PhanQuyen;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo tài khoản Admin mới';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TẠO TÀI KHOẢN ADMIN ===');
        $this->line('');

        $email = $this->argument('email') ?? $this->ask('Nhập email admin', 'admin@giasu.com');
        $password = $this->argument('password') ?? $this->secret('Nhập mật khẩu (tối thiểu 8 ký tự)');

        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Email không hợp lệ!');
            return 1;
        }

        if (strlen($password) < 8) {
            $this->error('❌ Mật khẩu phải có ít nhất 8 ký tự!');
            return 1;
        }

        // Kiểm tra email đã tồn tại chưa
        if (TaiKhoan::where('Email', $email)->exists()) {
            $this->error("❌ Email {$email} đã tồn tại trong hệ thống!");
            
            if ($this->confirm('Bạn có muốn reset mật khẩu cho tài khoản này?', false)) {
                return $this->resetAdminPassword($email, $password);
            }
            
            return 1;
        }

        try {
            DB::transaction(function () use ($email, $password) {
                // Tạo tài khoản
                $taiKhoan = TaiKhoan::create([
                    'Email' => $email,
                    'MatKhauHash' => Hash::make($password),
                    'SoDienThoai' => null,
                    'TrangThai' => 1, // Kích hoạt ngay
                ]);

                // Gán quyền Admin (VaiTroID = 1)
                PhanQuyen::create([
                    'TaiKhoanID' => $taiKhoan->TaiKhoanID,
                    'VaiTroID' => 1, // Admin
                ]);

                $this->line('');
                $this->info('✅ Tạo tài khoản Admin thành công!');
                $this->line('');
                $this->line('📋 THÔNG TIN ĐĂNG NHẬP:');
                $this->line("  📧 Email    : {$email}");
                $this->line("  🔐 Mật khẩu : {$password}");
                $this->line("  🆔 ID       : {$taiKhoan->TaiKhoanID}");
                $this->line("  👤 Vai trò  : Admin");
                $this->line('');
                $this->line('🌐 URL đăng nhập:');
                $this->line('  API   : POST http://127.0.0.1:8000/api/login');
                $this->line('  Admin : http://127.0.0.1:8000/admin/login');
                $this->line('');
            });

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Reset password cho tài khoản admin
     */
    private function resetAdminPassword($email, $password)
    {
        try {
            $taiKhoan = TaiKhoan::where('Email', $email)->first();
            
            // Kiểm tra xem có phải admin không
            $phanQuyen = PhanQuyen::where('TaiKhoanID', $taiKhoan->TaiKhoanID)->first();
            
            if (!$phanQuyen || $phanQuyen->VaiTroID != 1) {
                $this->error('❌ Tài khoản này không phải là Admin!');
                return 1;
            }

            // Reset mật khẩu
            $taiKhoan->MatKhauHash = Hash::make($password);
            $taiKhoan->TrangThai = 1; // Kích hoạt
            $taiKhoan->save();

            $this->line('');
            $this->info('✅ Reset mật khẩu Admin thành công!');
            $this->line('');
            $this->line('📋 THÔNG TIN ĐĂNG NHẬP:');
            $this->line("  📧 Email    : {$email}");
            $this->line("  🔐 Mật khẩu : {$password}");
            $this->line('');

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: " . $e->getMessage());
            return 1;
        }
    }
}
