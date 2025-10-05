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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mat_khau');
            $table->foreignId('vai_tro_id')->constrained('vai_tro')->onDelete('restrict');
            $table->boolean('kich_hoat')->default(true);
            $table->enum('loai_tai_khoan', ['khach_hang','nhan_vien','quan_ly'])->default('khach_hang');
            $table->integer('diem')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('email');
            $table->index('loai_tai_khoan');
            $table->index(['kich_hoat', 'loai_tai_khoan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
