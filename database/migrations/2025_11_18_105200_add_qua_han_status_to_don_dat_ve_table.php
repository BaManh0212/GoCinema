<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'qua_han' to the enum values for trang_thai in don_dat_ve
        DB::statement("ALTER TABLE `don_dat_ve` MODIFY `trang_thai` ENUM('cho_thanh_toan','da_thanh_toan','da_checkin','da_huy','qua_han') NOT NULL DEFAULT 'cho_thanh_toan';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum values (without 'qua_han')
        // Note: This will fail if there are any records with 'qua_han' status
        DB::statement("UPDATE `don_dat_ve` SET `trang_thai` = 'da_huy' WHERE `trang_thai` = 'qua_han';");
        DB::statement("ALTER TABLE `don_dat_ve` MODIFY `trang_thai` ENUM('cho_thanh_toan','da_thanh_toan','da_checkin','da_huy') NOT NULL DEFAULT 'cho_thanh_toan';");
    }
};
