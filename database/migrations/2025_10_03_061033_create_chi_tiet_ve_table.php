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
        Schema::create('chi_tiet_ve', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_dat_ve_id')->constrained('don_dat_ve')->onDelete('cascade');
            $table->foreignId('ghe_id')->constrained('ghe')->onDelete('restrict');
            $table->decimal('gia', 12, 2);
            $table->enum('loai_ghe', ['thuong','vip','doi']);
            $table->enum('trang_thai', ['cho_thanh_toan','da_thanh_toan','da_su_dung','da_huy'])->default('cho_thanh_toan');
            $table->dateTime('thoi_gian_su_dung')->nullable();
            $table->timestamps();

            // Unique constraint - prevent double booking same seat
            $table->unique(['don_dat_ve_id', 'ghe_id']);

            // Indexes
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_ve');
    }
};
