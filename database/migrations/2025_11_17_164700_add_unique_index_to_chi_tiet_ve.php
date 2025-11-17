<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::select("SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = DATABASE() AND table_name = 'chi_tiet_ve' AND index_name = 'ctv_suatchieu_ghe_unique' LIMIT 1");
        if (empty($exists)) {
            Schema::table('chi_tiet_ve', function (Blueprint $table) {
                $table->unique(['suat_chieu_id','ghe_id'], 'ctv_suatchieu_ghe_unique');
            });
        }
    }

    public function down(): void
    {
        $exists = DB::select("SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = DATABASE() AND table_name = 'chi_tiet_ve' AND index_name = 'ctv_suatchieu_ghe_unique' LIMIT 1");
        if (!empty($exists)) {
            Schema::table('chi_tiet_ve', function (Blueprint $table) {
                $table->dropUnique('ctv_suatchieu_ghe_unique');
            });
        }
    }
};
