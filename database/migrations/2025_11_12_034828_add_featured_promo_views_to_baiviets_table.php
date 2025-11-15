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
            if (!Schema::hasColumn('baiviets', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('baiviets', 'is_promo')) {
                $table->boolean('is_promo')->default(false);
            }
            if (!Schema::hasColumn('baiviets', 'views')) {
                $table->integer('views')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baiviets', function (Blueprint $table) {
            if (Schema::hasColumn('baiviets', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            if (Schema::hasColumn('baiviets', 'is_promo')) {
                $table->dropColumn('is_promo');
            }
            if (Schema::hasColumn('baiviets', 'views')) {
                $table->dropColumn('views');
            }
        });
    }
};
