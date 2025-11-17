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
        Schema::create('chi_tiet_ve', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('don_dat_ve_id');
            $table->unsignedBigInteger('suat_chieu_id');
            $table->unsignedBigInteger('ghe_id');

            $table->decimal('gia', 12, 2);

            $table->enum('loai_ghe', ['thuong','vip','doi']);
            $table->enum('trang_thai', ['cho_thanh_toan','da_thanh_toan','da_su_dung','da_huy']);

            $table->dateTime('thoi_gian_su_dung')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('don_dat_ve_id')->references('id')->on('don_dat_ve')->onDelete('cascade');
            $table->foreign('suat_chieu_id')->references('id')->on('suat_chieu')->onDelete('cascade');
            $table->foreign('ghe_id')->references('id')->on('ghe')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_ve');
    }
};
