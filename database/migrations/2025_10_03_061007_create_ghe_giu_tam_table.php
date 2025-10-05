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
        Schema::create('ghe_giu_tam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suat_chieu_id')->constrained('suat_chieu')->onDelete('cascade');
            $table->foreignId('ghe_id')->constrained('ghe')->onDelete('cascade');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->dateTime('het_han');
            $table->enum('trang_thai', ['dang_giu', 'da_xac_nhan', 'da_huy', 'het_han'])->default('dang_giu');
            $table->timestamps();

            // Unique constraint - một ghế chỉ được giữ bởi 1 người trong 1 suất
            $table->unique(['ghe_id', 'suat_chieu_id']);

            // Indexes
            $table->index('het_han');
            $table->index(['het_han', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghe_giu_tam');
    }
};
