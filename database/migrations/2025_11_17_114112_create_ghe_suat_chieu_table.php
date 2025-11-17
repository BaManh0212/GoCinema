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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghe_suat_chieu');
    }
};
