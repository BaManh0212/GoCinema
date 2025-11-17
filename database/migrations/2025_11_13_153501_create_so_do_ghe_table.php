<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('so_do_ghe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_id') // tạo cột phong_id + FK
                  ->constrained('phong_chieu')
                  ->cascadeOnDelete();
            $table->text('ma_tran'); // JSON lưu ma trận ghế
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('so_do_ghe');
    }
};
