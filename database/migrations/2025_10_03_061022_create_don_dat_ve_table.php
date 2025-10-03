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
        Schema::create('don_dat_ve', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don')->unique();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung');
            $table->foreignId('suat_chieu_id')->constrained('suat_chieu');
            $table->foreignId('ma_giam_gia_id')->nullable()->constrained('ma_giam_gia');
            $table->decimal('tong_tien', 12, 2);
            $table->enum('trang_thai', ['cho_thanh_toan','da_thanh_toan','da_huy','dat_coc']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_dat_ve');
    }
};
