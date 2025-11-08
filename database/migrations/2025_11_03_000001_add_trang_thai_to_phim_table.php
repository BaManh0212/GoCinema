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
        // Kiểm tra nếu cột chưa tồn tại thì thêm
        if (!Schema::hasColumn('phim', 'trang_thai')) {
            Schema::table('phim', function (Blueprint $table) {
                $table->tinyInteger('trang_thai')->default(1)->index();
            });
        }
        
        // Thêm cột luot_xem nếu chưa có
        if (!Schema::hasColumn('phim', 'luot_xem')) {
            Schema::table('phim', function (Blueprint $table) {
                $table->integer('luot_xem')->default(0)->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phim', function (Blueprint $table) {
            if (Schema::hasColumn('phim', 'trang_thai')) {
                $table->dropColumn('trang_thai');
            }
            if (Schema::hasColumn('phim', 'luot_xem')) {
                $table->dropColumn('luot_xem');
            }
        });
    }
};
