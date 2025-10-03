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
        Schema::create('voucher_nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung');
            $table->foreignId('voucher_id')->constrained('voucher');
            $table->integer('diem_da_doi')->default(0);
            $table->integer('so_lan_da_dung')->default(0);
            $table->dateTime('ngay_doi')->nullable();
            $table->dateTime('ngay_han')->nullable();
            $table->enum('trang_thai', ['chua_su_dung','da_su_dung','da_het_han'])->default('chua_su_dung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_nguoi_dung');
    }
};
