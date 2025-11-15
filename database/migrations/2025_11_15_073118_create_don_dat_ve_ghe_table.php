<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('don_dat_ve_ghe', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại liên kết đơn đặt vé
            $table->foreignId('don_dat_ve_id')
                  ->constrained('don_dat_ve')
                  ->onDelete('cascade');

            // Khóa ngoại liên kết ghế
            $table->foreignId('ghe_id')
                  ->constrained('ghe')
                  ->onDelete('cascade');

            // Giá của ghế tại thời điểm đặt
            $table->integer('gia')->default(0);

            $table->timestamps();

            // Đảm bảo mỗi ghế chỉ được đặt 1 lần cho 1 đơn
            $table->unique(['don_dat_ve_id', 'ghe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_dat_ve_ghe');
    }
};
