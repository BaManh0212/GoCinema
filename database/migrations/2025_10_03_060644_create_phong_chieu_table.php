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
        Schema::create('phong_chieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rap_id')->constrained('rap')->onDelete('cascade');
            $table->string('ten');
            $table->integer('tong_ghe')->default(0);
            $table->text('so_do')->nullable();
            $table->foreignId('dinh_dang_id')->constrained('dinh_dang')->onDelete('cascade');
            $table->enum('trang_thai', ['hoat_dong','bao_tri','ngung_su_dung'])->default('hoat_dong');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong_chieu');
    }
};
