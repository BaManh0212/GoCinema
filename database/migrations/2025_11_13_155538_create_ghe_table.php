<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('so_do_id')->constrained('so_do_ghe')->onDelete('cascade'); // liên kết sơ đồ
            $table->string('ten');       // Ví dụ: A1, B2
            $table->string('hang');      // Hàng ghế: A, B, C...
            $table->integer('cot');      // Cột ghế: 1,2,3...
            $table->enum('loai', ['thuong','vip','doi'])->default('thuong'); 
            $table->enum('trang_thai', ['hoat_dong','bao_tri'])->default('hoat_dong');
            $table->timestamps();
            $table->unique(['so_do_id','hang','cot']); // mỗi ghế duy nhất trong sơ đồ
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghe');
    }
};
