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
        Schema::create('bao_cao_doanh_thu_rap', function (Blueprint $table) {
            $table->id();
            $table->date('ngay');
            $table->foreignId('rap_id')->constrained('rap');
            $table->integer('tong_ve')->default(0);
            $table->decimal('tong_tien', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bao_cao_doanh_thu_rap');
    }
};
