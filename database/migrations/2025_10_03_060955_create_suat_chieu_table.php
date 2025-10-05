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
        Schema::create('suat_chieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phim_id')->constrained('phim')->onDelete('restrict');
            $table->foreignId('phong_id')->constrained('phong_chieu')->onDelete('restrict');
            $table->dateTime('gio_bat_dau');
            $table->dateTime('gio_ket_thuc');
            $table->decimal('gia_ve', 12, 2);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('phim_id');
            $table->index('phong_id');
            $table->index('gio_bat_dau');
            $table->index(['gio_bat_dau', 'gio_ket_thuc']);

            // Prevent overlapping showtimes in same room
            $table->index(['phong_id', 'gio_bat_dau', 'gio_ket_thuc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suat_chieu');
    }
};
