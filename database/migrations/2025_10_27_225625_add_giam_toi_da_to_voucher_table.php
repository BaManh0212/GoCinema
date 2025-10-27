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
        Schema::table('voucher', function (Blueprint $table) {
            $table->decimal('giam_toi_da', 12, 2)->nullable()->after('gia_tri')
                  ->comment('Giảm tối đa (cho voucher %), VD: Giảm 10% tối đa 50k');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher', function (Blueprint $table) {
            $table->dropColumn('giam_toi_da');
        });
    }
};
