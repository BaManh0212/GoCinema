<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phim', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de', 255)->index();
            $table->string('slug', 255)->unique();
            $table->text('mo_ta')->nullable();
            $table->string('anh_poster')->nullable();
            $table->string('banner')->nullable();
            $table->string('trailer')->nullable();
            $table->boolean('phu_de')->default(false);
            $table->integer('thoi_luong')->nullable();
            $table->date('ngay_cong_chieu')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->string('dao_dien')->nullable();
            $table->text('dien_vien')->nullable();
            $table->string('do_tuoi_gioi_han', 10)->nullable();
            $table->string('dinh_dang', 10)->default('2D');
            $table->tinyInteger('trang_thai')->default(1)->index();
            $table->decimal('danh_gia', 3, 1)->default(0);
            $table->integer('luot_xem')->default(0);
            $table->unsignedBigInteger('danh_muc_id')->nullable();
            $table->unsignedBigInteger('ngon_ngu_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('danh_muc_id')->references('id')->on('danh_muc')->onDelete('set null');
            $table->foreign('ngon_ngu_id')->references('id')->on('ngon_ngu')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phim');
    }
};
