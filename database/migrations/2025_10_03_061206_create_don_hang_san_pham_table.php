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
        Schema::create('don_hang_san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_dat_ve_id')->constrained('don_dat_ve');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->integer('so_luong');
            $table->decimal('gia', 12, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_hang_san_pham');
    }
};
