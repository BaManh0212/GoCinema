<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinPrintLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checkin_print_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Admin user who performed the action');
            $table->unsignedBigInteger('don_dat_ve_id')->comment('Booking order id');
            $table->string('action_type')->comment('Action type: checkin or print');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('don_dat_ve_id')->references('id')->on('don_dat_ves')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_print_logs');
    }
}
