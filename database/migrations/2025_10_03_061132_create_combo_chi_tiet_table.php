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
        Schema::create('combo_chi_tiet', function (Blueprint $table) {
            $table->foreign('combo_id')->references('id')->on('combo')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->integer('so_luong')->default(1);
            $table->primary(['combo_id','san_pham_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_chi_tiet');
    }
};
