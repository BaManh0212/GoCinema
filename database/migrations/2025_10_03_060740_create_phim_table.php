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
            $table->id();
            $table->string('tieu_de');
            $table->text('mo_ta')->nullable();
            $table->string('anh_poster')->nullable();
            $table->string('trailer')->nullable();
            $table->boolean('phu_de')->default(false);
            $table->integer('thoi_luong')->nullable();
            $table->date('ngay_cong_chieu')->nullable();
            $table->integer('do_tuoi_gioi_han')->nullable();
            $table->foreignId('danh_muc_id')->nullable()->constrained('danh_muc')->onDelete('set null');
            $table->foreignId('ngon_ngu_id')->nullable()->constrained('ngon_ngu')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tieu_de');
            $table->index('ngay_cong_chieu');
            $table->index('danh_muc_id');
            $table->index(['ngay_cong_chieu', 'deleted_at']);
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
