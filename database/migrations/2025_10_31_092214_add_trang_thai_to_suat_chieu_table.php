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
        Schema::table('suat_chieu', function (Blueprint $table) {
            // 🟢 Thêm cột 'trang_thai' nếu chưa có
            $table->enum('trang_thai', ['hoat_dong', 'tam_dung', 'huy'])
                  ->default('hoat_dong')
                  ->after('gia_ve')
                  ->comment('Trạng thái suất chiếu: hoat_dong, tam_dung, huy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suat_chieu', function (Blueprint $table) {
            // 🔴 Xóa cột nếu rollback
            $table->dropColumn('trang_thai');
        });
    }
};
