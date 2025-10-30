<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nếu cột poster tồn tại thì drop
        if (Schema::hasColumn('phim', 'poster')) {
            Schema::table('phim', function (Blueprint $table) {
                $table->dropColumn('poster');
            });
        }
    }

    public function down(): void
    {
        // Hoàn tác: thêm cột poster trở lại (nullable)
        Schema::table('phim', function (Blueprint $table) {
            if (!Schema::hasColumn('phim', 'poster')) {
                $table->string('poster')->nullable()->after('tieu_de');
            }
        });
    }
};
