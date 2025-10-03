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
        Schema::create('vai_tro_quyen_han', function (Blueprint $table) {
            $table->foreignId('vai_tro_id')->constrained('vai_tro');
            $table->foreignId('quyen_han_id')->constrained('quyen_han');
            $table->primary(['vai_tro_id','quyen_han_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vai_tro_quyen_han');
    }
};
