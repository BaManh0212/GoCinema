<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baiviets', function (Blueprint $table) {
            $table->integer('thoi_luong')->nullable();
            $table->string('dao_dien')->nullable();
            $table->string('dien_vien')->nullable();
            $table->string('ngon_ngu')->nullable();
            $table->string('dinh_dang')->nullable();
            $table->string('phu_de')->nullable();
            $table->string('gioi_han_tuoi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('baiviets', function (Blueprint $table) {
            $table->dropColumn(['thoi_luong', 'dao_dien', 'dien_vien', 'ngon_ngu', 'dinh_dang', 'phu_de', 'gioi_han_tuoi']);
        });
    }
};
