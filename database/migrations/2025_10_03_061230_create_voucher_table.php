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
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->enum('loai', ['phan_tram','so_tien']);
            $table->decimal('gia_tri', 12, 2);
            $table->decimal('gia_tri_don_hang_toi_thieu', 12, 2)->nullable();
            $table->enum('ap_dung_cho', ['ve','san_pham','tat_ca'])->default('tat_ca');
            $table->integer('so_lan_su_dung')->default(1);
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};
