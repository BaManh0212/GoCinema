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
        Schema::create('phim_the_loai', function (Blueprint $table) {
            $table->foreignId('phim_id')->constrained('phim');
            $table->foreignId('the_loai_id')->constrained('the_loai');
            $table->primary(['phim_id','the_loai_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim_the_loai');
    }
};
