<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suat_chieu', function (Blueprint $table) {
            $table->string('ly_do_huy')->nullable()->after('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::table('suat_chieu', function (Blueprint $table) {
            $table->dropColumn('ly_do_huy');
        });
    }
};
