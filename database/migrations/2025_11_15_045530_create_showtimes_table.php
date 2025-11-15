<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            if (!Schema::hasColumn('showtimes', 'seat_type')) {
                $table->string('seat_type')->nullable()->after('price');
            }
            if (!Schema::hasColumn('showtimes', 'seats_available')) {
                $table->integer('seats_available')->default(0)->after('seat_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            if (Schema::hasColumn('showtimes', 'seat_type')) {
                $table->dropColumn('seat_type');
            }
            if (Schema::hasColumn('showtimes', 'seats_available')) {
                $table->dropColumn('seats_available');
            }
        });
    }
};
