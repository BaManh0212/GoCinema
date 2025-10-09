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
    Schema::create('phim', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('tieu_de');
        $table->text('mo_ta')->nullable();
        $table->string('anh_poster')->nullable();
        $table->string('trailer')->nullable();
        $table->boolean('phu_de')->default(false);
        $table->integer('thoi_luong')->nullable();
        $table->date('ngay_cong_chieu')->nullable();
        $table->integer('do_tuoi_gioi_han')->nullable();
        $table->unsignedBigInteger('danh_muc_id')->nullable();
        $table->unsignedBigInteger('ngon_ngu_id')->nullable();
        $table->dateTime('ngay_tao')->useCurrent();
        $table->dateTime('ngay_cap_nhat')->nullable();
        $table->dateTime('ngay_xoa')->nullable();

        $table->foreign('danh_muc_id')->references('id')->on('danh_muc')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim');
    }
};
