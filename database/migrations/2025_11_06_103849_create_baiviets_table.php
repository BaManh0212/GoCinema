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
            $table->string('tieu_de');
            $table->string('slug')->unique();
            $table->string('hinh_anh')->nullable();
            $table->text('tom_tat')->nullable();
            $table->longText('noi_dung');
            $table->enum('loai', ['tin-tuc', 'khuyen-mai'])->default('tin-tuc');
            $table->date('ngay_phat_hanh')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_promo')->default(false);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baiviets');
    }
};
