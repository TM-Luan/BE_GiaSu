<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('LopHocYeuCau', function (Blueprint $table) {
            
            // Bước 1: Xóa khóa ngoại trước
            // Tên khóa ngoại trong file SQL của bạn là LopHocYeuCau_ibfk_6
            try {
                 $table->dropForeign('LopHocYeuCau_ibfk_6');
            } catch (\Exception $e) {
                 echo "Không tìm thấy khóa ngoại 'LopHocYeuCau_ibfk_6'. Thử tên khác... \n";
                 try {
                    // Tên mặc định của Laravel
                    $table->dropForeign('lophocyeucau_thoigiandayid_foreign');
                 } catch (\Exception $ex) {
                    echo "Lỗi: Không thể xóa khóa ngoại: " . $ex->getMessage();
                 }
            }

            // Bước 2: Xóa cột cũ
            $table->dropColumn('ThoiGianDayID');

            // Bước 3: Thêm 2 cột mới (cho phép NULL)
            $table->integer('SoBuoiTuan')->nullable()->after('DoiTuongID');
            $table->string('LichHocMongMuon', 255)->nullable()->after('SoBuoiTuan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('LopHocYeuCau', function (Blueprint $table) {
            $table->dropColumn('SoBuoiTuan');
            $table->dropColumn('LichHocMongMuon');

            $table->integer('ThoiGianDayID');
            // $table->foreign('ThoiGianDayID')->references('ThoiGianDayID')->on('ThoiGianDay');
        });
    }
};