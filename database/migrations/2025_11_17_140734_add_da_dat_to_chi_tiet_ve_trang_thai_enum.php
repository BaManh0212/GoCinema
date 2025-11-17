<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm 'da_dat' vào enum trang_thai của chi_tiet_ve
        DB::statement("ALTER TABLE chi_tiet_ve MODIFY COLUMN trang_thai ENUM('cho_thanh_toan','da_thanh_toan','da_su_dung','da_huy','da_dat') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa 'da_dat' khỏi enum trang_thai của chi_tiet_ve
        DB::statement("ALTER TABLE chi_tiet_ve MODIFY COLUMN trang_thai ENUM('cho_thanh_toan','da_thanh_toan','da_su_dung','da_huy') NOT NULL");
    }
};
