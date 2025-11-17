<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghe_suat_chieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suat_chieu_id')->constrained()->onDelete('cascade');
            $table->foreignId('so_do_id')->constrained('so_do_ghe')->onDelete('cascade');
            $table->string('hang', 5);
            $table->integer('cot');
            $table->string('loai', 20)->default('thuong');
            $table->string('trang_thai', 20)->default('hoat_dong');
            $table->timestamps();

            // Đảm bảo không có ghế trùng lặp trong cùng suất chiếu và sơ đồ
            $table->unique(['suat_chieu_id', 'so_do_id', 'hang', 'cot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghe_suat_chieu');
    }
};
