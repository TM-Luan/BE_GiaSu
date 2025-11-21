<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('LopHocYeuCau', function (Blueprint $table) {
            // Thêm cột TrangThaiThanhToan để đồng bộ với mobile
            $table->enum('TrangThaiThanhToan', ['ChuaThanhToan', 'DaThanhToan'])
                  ->default('ChuaThanhToan')
                  ->after('TrangThai')
                  ->comment('Trạng thái thanh toán phí nhận lớp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('LopHocYeuCau', function (Blueprint $table) {
            $table->dropColumn('TrangThaiThanhToan');
        });
    }
};
