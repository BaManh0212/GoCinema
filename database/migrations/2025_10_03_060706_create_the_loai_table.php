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
        Schema::create('the_loai', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->string('mo_ta')->nullable();
            $table->timestamps();
            $table->softDeletes(); // ngay_xoa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('the_loai');
    }
};
