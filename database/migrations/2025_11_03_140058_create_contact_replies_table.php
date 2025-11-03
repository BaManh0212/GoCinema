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
    Schema::create('contact_replies', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('contact_id');
        $table->unsignedBigInteger('admin_id');
        $table->text('reply_message');
        $table->timestamps();

        $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_replies');
    }
};
