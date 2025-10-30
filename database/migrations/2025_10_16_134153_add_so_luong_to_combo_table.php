<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoLuongToComboTable extends Migration
{
    public function up()
    {
        Schema::table('combo', function (Blueprint $table) {
            $table->integer('so_luong')->default(0)->after('mo_ta'); // Thêm cột số lượng
        });
    }

    public function down()
    {
        Schema::table('combo', function (Blueprint $table) {
            $table->dropColumn('so_luong');
        });
    }
}
