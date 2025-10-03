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
            $table->foreignId('phim_id')->constrained('phim');
            $table->foreignId('phong_id')->constrained('phong_chieu');
            $table->dateTime('gio_bat_dau');
            $table->dateTime('gio_ket_thuc');
            $table->decimal('gia_ve', 12, 2);
            $table->timestamps();
            $table->softDeletes(); // ngay_xoa
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
