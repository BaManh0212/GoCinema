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
        Schema::table('phong_chieu', function (Blueprint $table) {
            $table->integer('so_hang')->default(10)->after('so_do');
            $table->integer('so_cot')->default(15)->after('so_hang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phong_chieu', function (Blueprint $table) {
            $table->dropColumn(['so_hang', 'so_cot']);
        });
    }
};
