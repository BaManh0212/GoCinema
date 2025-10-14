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
    Schema::table('phim', function (Blueprint $table) {
        $table->string('dao_dien')->nullable()->after('ngay_cong_chieu');
        $table->text('dien_vien')->nullable()->after('dao_dien');
    });
}

public function down(): void
{
    Schema::table('phim', function (Blueprint $table) {
        $table->dropColumn(['dao_dien', 'dien_vien']);
    });
}

};
