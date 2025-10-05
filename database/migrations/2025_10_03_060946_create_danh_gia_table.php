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
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phim_id')->constrained('phim')->onDelete('cascade');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->tinyInteger('so_sao')->check('so_sao BETWEEN 1 AND 5');
            $table->text('binh_luan')->nullable();
            $table->timestamps();

            // Unique constraint - mỗi user chỉ đánh giá 1 lần cho 1 phim
            $table->unique(['phim_id', 'nguoi_dung_id']);

            // Indexes
            $table->index('phim_id');
            $table->index('so_sao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia');
    }
};
