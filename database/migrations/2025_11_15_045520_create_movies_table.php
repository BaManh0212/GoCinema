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
        Schema::table('movies', function (Blueprint $table) {
            if (!Schema::hasColumn('movies', 'release_date')) {
                $table->date('release_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('movies', 'rating')) {
                $table->decimal('rating', 3, 1)->nullable()->after('release_date');
            }
            if (!Schema::hasColumn('movies', 'language')) {
                $table->string('language')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('movies', 'age_rating')) {
                $table->string('age_rating', 10)->nullable()->after('language');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            if (Schema::hasColumn('movies', 'release_date')) {
                $table->dropColumn('release_date');
            }
            if (Schema::hasColumn('movies', 'rating')) {
                $table->dropColumn('rating');
            }
            if (Schema::hasColumn('movies', 'language')) {
                $table->dropColumn('language');
            }
            if (Schema::hasColumn('movies', 'age_rating')) {
                $table->dropColumn('age_rating');
            }
        });
    }
};
