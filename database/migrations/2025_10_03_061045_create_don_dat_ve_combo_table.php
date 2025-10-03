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
        Schema::create('don_dat_ve_combo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_dat_ve_id')->constrained('don_dat_ve');
            $table->foreignId('combo_id')->constrained('combo');
            $table->integer('so_luong');
            $table->decimal('gia', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_dat_ve_combo');
    }
};
