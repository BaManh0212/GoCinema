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
        Schema::create('phim_dao_dien', function (Blueprint $table) {
            $table->foreignId('phim_id')->constrained('phim');
            $table->foreignId('dao_dien_id')->constrained('dao_dien');
            $table->primary(['phim_id','dao_dien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim_dao_dien');
    }
};
