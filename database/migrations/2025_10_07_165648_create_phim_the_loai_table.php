<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phim_the_loai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phim_id');
            $table->unsignedBigInteger('the_loai_id');
            $table->timestamps();

            $table->foreign('phim_id')->references('id')->on('phim')->onDelete('cascade');
            $table->foreign('the_loai_id')->references('id')->on('the_loai')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phim_the_loai');
    }
};
