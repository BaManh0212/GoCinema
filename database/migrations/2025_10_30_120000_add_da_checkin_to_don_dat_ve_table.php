<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'da_checkin' to the enum values for trang_thai in don_dat_ve
        // Use raw statement because changing enum types requires direct SQL in many MySQL setups
        DB::statement("ALTER TABLE `don_dat_ve` MODIFY `trang_thai` ENUM('cho_thanh_toan','da_thanh_toan','da_checkin','da_huy') NOT NULL DEFAULT 'cho_thanh_toan';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous set (without da_checkin). Note: this will fail if any rows use da_checkin.
        DB::statement("ALTER TABLE `don_dat_ve` MODIFY `trang_thai` ENUM('cho_thanh_toan','da_thanh_toan','da_huy') NOT NULL DEFAULT 'cho_thanh_toan';");
    }
};
