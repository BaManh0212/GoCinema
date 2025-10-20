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
            $table->integer('diem_can')->default(0)->after('so_lan_su_dung')->comment('Số điểm cần để đổi voucher');
            $table->boolean('kich_hoat')->default(true)->after('diem_can')->comment('Trạng thái kích hoạt voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher', function (Blueprint $table) {
            $table->dropColumn(['diem_can', 'kich_hoat']);
        });
    }
};
