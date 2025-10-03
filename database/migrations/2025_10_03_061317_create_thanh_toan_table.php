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
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_dat_ve_id')->constrained('don_dat_ve');
            $table->foreignId('phuong_thuc_id')->constrained('phuong_thuc_thanh_toan');
            $table->decimal('so_tien', 12, 2);
            $table->string('ma_giao_dich')->nullable();
            $table->enum('trang_thai', ['dang_xu_ly','thanh_cong','that_bai','hoan_tien'])->default('dang_xu_ly');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
