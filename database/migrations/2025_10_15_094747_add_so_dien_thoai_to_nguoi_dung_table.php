<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('nguoi_dung', function (Blueprint $table) {
        $table->string('so_dien_thoai', 15)->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('nguoi_dung', function (Blueprint $table) {
        $table->dropColumn('so_dien_thoai');
    });
}
};
