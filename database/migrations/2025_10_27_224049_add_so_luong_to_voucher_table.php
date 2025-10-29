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
        Schema::table('voucher', function (Blueprint $table) {
            $table->integer('so_luong_toi_da')->default(100)->after('so_lan_su_dung')->comment('Số lượng voucher có sẵn (trần)');
            $table->integer('so_luong_da_dung')->default(0)->after('so_luong_toi_da')->comment('Số lượng đã được đổi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher', function (Blueprint $table) {
            $table->dropColumn(['so_luong_toi_da', 'so_luong_da_dung']);
        });
    }
};
