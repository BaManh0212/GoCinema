<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            if (!Schema::hasColumn('nguoi_dung', 'trang_thai')) {

                if (Schema::hasColumn('nguoi_dung', 'diem')) {
                    // Nếu thực sự có cột 'diem' thì mới đặt sau
                    $table->boolean('trang_thai')->default(1)->after('diem');
                } else {
                    // Không thì cứ thêm bình thường
                    $table->boolean('trang_thai')->default(1);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            if (Schema::hasColumn('nguoi_dung', 'trang_thai')) {
                $table->dropColumn('trang_thai');
            }
        });
    }
};
