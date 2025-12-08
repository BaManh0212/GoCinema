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
        // Drop the ghe_suat_chieu table
        // Seat maintenance is now managed at room level (ghe.trang_thai), not per showtime
        Schema::dropIfExists('ghe_suat_chieu');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table if needed (for rollback)
        Schema::create('ghe_suat_chieu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suat_chieu_id');
            $table->unsignedBigInteger('ghe_id');
            $table->enum('trang_thai', ['hoat_dong', 'bao_tri', 'vo_hieu_hoa'])->default('hoat_dong');
            $table->timestamps();

            $table->foreign('suat_chieu_id')->references('id')->on('suat_chieu')->onDelete('cascade');
            $table->foreign('ghe_id')->references('id')->on('ghe')->onDelete('cascade');
            $table->unique(['suat_chieu_id', 'ghe_id']);
        });
    }
};
