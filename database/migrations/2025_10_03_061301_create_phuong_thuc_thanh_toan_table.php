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
        Schema::create('phuong_thuc_thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->string('ten');   // zalopay, vnpay, cod,...
            $table->string('mo_ta')->nullable();
            $table->enum('trang_thai', ['hoat_dong','khong_hoat_dong'])->default('hoat_dong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phuong_thuc_thanh_toan');
    }
};
