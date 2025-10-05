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
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('restrict');
            $table->foreignId('suat_chieu_id')->constrained('suat_chieu')->onDelete('restrict');
            $table->foreignId('ma_giam_gia_id')->nullable()->constrained('ma_giam_gia')->onDelete('set null');
            $table->decimal('tong_tien', 12, 2);
            $table->enum('trang_thai', ['cho_thanh_toan','da_thanh_toan','da_huy','dat_coc'])->default('cho_thanh_toan');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nguoi_dung_id');
            $table->index('suat_chieu_id');
            $table->index('trang_thai');
            $table->index('created_at');
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
