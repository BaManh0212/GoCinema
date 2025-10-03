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
            $table->foreignId('phim_id')->constrained('phim');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung');
            $table->tinyInteger('so_sao')->check('so_sao BETWEEN 1 AND 5');
            $table->text('binh_luan')->nullable();
            $table->timestamps();
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
