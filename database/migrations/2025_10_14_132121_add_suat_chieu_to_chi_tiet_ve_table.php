<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Thêm cột (nullable tạm để backfill)
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            if (!Schema::hasColumn('chi_tiet_ve', 'suat_chieu_id')) {
                $table->unsignedBigInteger('suat_chieu_id')->nullable()->after('don_dat_ve_id');
            }
        });

        // 2) Backfill từ don_dat_ve -> chi_tiet_ve
        DB::statement("
            UPDATE chi_tiet_ve ct
            JOIN don_dat_ve d ON d.id = ct.don_dat_ve_id
            SET ct.suat_chieu_id = d.suat_chieu_id
            WHERE ct.suat_chieu_id IS NULL
        ");

        // 2.1) Chặn nếu vẫn còn NULL (dữ liệu bẩn)
        $dangConNull = DB::table('chi_tiet_ve')->whereNull('suat_chieu_id')->count();
        if ($dangConNull > 0) {
            throw new \RuntimeException(
                'Còn ' . $dangConNull . ' dòng chi_tiet_ve chưa có suat_chieu_id. Sửa dữ liệu rồi chạy lại migrate.'
            );
        }

        // 3) Đổi sang NOT NULL (không có FK nào cản trở ở bước này)
        DB::statement("ALTER TABLE chi_tiet_ve MODIFY COLUMN suat_chieu_id BIGINT UNSIGNED NOT NULL");

        // 4) Tạo FK và các chỉ mục/unique
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            // Giữ lịch sử: suat_chieu đang dùng soft deletes nên xóa logic, không kích hoạt onDelete.
            // Dùng restrictOnDelete để ngăn xóa cứng suất chieu khi còn vé (an toàn dữ liệu).
            $table->foreign('suat_chieu_id', 'ctv_suatchieu_fk')
                ->references('id')->on('suat_chieu')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Chống đặt trùng ghế cùng suất chiếu
            $table->unique(['suat_chieu_id', 'ghe_id'], 'ctv_suatchieu_ghe_unique');

            // Index phục vụ báo cáo
            $table->index(['don_dat_ve_id', 'loai_ghe'], 'ctv_don_loai_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            if (Schema::hasColumn('chi_tiet_ve', 'suat_chieu_id')) {
                $table->dropUnique('ctv_suatchieu_ghe_unique');
                $table->dropIndex('ctv_don_loai_idx');
                $table->dropForeign('ctv_suatchieu_fk');
                $table->dropColumn('suat_chieu_id');
            }
        });
    }
};
