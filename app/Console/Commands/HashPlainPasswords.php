<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;

class HashPlainPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:hash-plain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash tất cả mật khẩu plain text trong database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang kiểm tra và hash các mật khẩu plain text...');

        $taiKhoans = TaiKhoan::all();
        $count = 0;

        foreach ($taiKhoans as $tk) {
            // Kiểm tra nếu mật khẩu chưa được hash (không bắt đầu với $2y$)
            if ($tk->MatKhauHash && !str_starts_with($tk->MatKhauHash, '$2y$')) {
                $plainPassword = $tk->MatKhauHash;
                $tk->MatKhauHash = Hash::make($plainPassword);
                $tk->save();
                $count++;
                $this->line("✓ Đã hash mật khẩu cho: {$tk->Email}");
            }
        }

        if ($count > 0) {
            $this->info("✓ Hoàn thành! Đã hash {$count} mật khẩu.");
        } else {
            $this->info('✓ Không có mật khẩu plain text nào cần hash.');
        }

        return 0;
    }
}
