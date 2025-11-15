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
        Schema::table('contact_replies', function (Blueprint $table) {
            // drop existing foreign if exists
            try {
                $table->dropForeign(['admin_id']);
            } catch (\Exception $e) {
                // ignore if doesn't exist
            }

            // ensure column unsignedBigInteger (already exists), then add FK to nguoi_dung
            $table->foreign('admin_id')
                ->references('id')
                ->on('nguoi_dung')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_replies', function (Blueprint $table) {
            try {
                $table->dropForeign(['admin_id']);
            } catch (\Exception $e) {
            }

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
