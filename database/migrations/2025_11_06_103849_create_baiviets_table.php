<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baiviets', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');  // VARCHAR không default nếu muốn
            $table->string('slug')->unique();
            $table->string('hinh_anh')->nullable();
            $table->text('tom_tat')->nullable();
            $table->longText('noi_dung'); // KHÔNG đặt default value
            $table->enum('loai', ['tin-tuc', 'khuyen-mai'])->default('tin-tuc');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baiviets');
    }
};
