<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_chi_tiet', function (Blueprint $table) {
            $table->id();

            // 🔹 Thêm combo_id trước khi dùng
            $table->foreignId('combo_id')
                  ->constrained('combo')
                  ->onDelete('cascade');

            $table->foreignId('san_pham_id')
                  ->constrained('san_pham')
                  ->onDelete('cascade');

            $table->integer('so_luong')->default(1);

            // (tùy chọn) Nếu bạn không cần id tự tăng thì dùng khóa chính kép:
            // $table->primary(['combo_id', 'san_pham_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_chi_tiet');
    }
};
