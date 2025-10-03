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
        Schema::create('ghe_giu_tam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suat_chieu_id')->constrained('suat_chieu');
            $table->foreignId('ghe_id')->constrained('ghe');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung');
            $table->dateTime('het_han');
            $table->timestamps();
            $table->unique(['ghe_id','suat_chieu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghe_giu_tam');
    }
};
