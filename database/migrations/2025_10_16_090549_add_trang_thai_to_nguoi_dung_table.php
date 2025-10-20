<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->boolean('trang_thai')->default(1)->after('diem'); // 1 = hoạt động, 0 = bị khóa
        });
    }

    public function down()
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
