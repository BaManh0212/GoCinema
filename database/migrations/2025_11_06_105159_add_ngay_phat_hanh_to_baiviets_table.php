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
        Schema::table('baiviets', function (Blueprint $table) {
            if (!Schema::hasColumn('baiviets', 'ngay_phat_hanh')) {
                $table->date('ngay_phat_hanh')->nullable()->after('loai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baiviets', function (Blueprint $table) {
            if (Schema::hasColumn('baiviets', 'ngay_phat_hanh')) {
                $table->dropColumn('ngay_phat_hanh');
            }
        });
    }
};
