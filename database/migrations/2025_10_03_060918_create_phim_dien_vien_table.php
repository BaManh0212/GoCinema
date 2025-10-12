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
Schema::create('phim_dien_vien', function (Blueprint $table) {
    $table->id();
    $table->foreignId('phim_id')->constrained('phim')->onDelete('cascade');
    $table->foreignId('dien_vien_id')->constrained('dien_vien')->onDelete('cascade');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim_dien_vien');
    }
};
