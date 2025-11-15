<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bao_cao_doanh_thu_rap', function (Blueprint $t) {
            $t->unique(['ngay', 'rap_id']);
            $t->index('ngay');
        });
        Schema::table('bao_cao_doanh_thu_phim', function (Blueprint $t) {
            $t->unique(['ngay', 'phim_id']);
            $t->index('ngay');
        });
        Schema::table('bao_cao_doanh_thu_nhan_vien', function (Blueprint $t) {
            $t->unique(['ngay', 'nhan_vien_id']);
            $t->index('ngay');
        });
        Schema::table('bao_cao_doanh_thu_suat', function (Blueprint $t) {
            $t->unique(['suat_chieu_id']);
        });
    }
    public function down(): void
    {
        Schema::table('bao_cao_doanh_thu_rap', fn(Blueprint $t) => $t->dropUnique(['bao_cao_doanh_thu_rap_ngay_rap_id_unique']));
        Schema::table('bao_cao_doanh_thu_phim', fn(Blueprint $t) => $t->dropUnique(['bao_cao_doanh_thu_phim_ngay_phim_id_unique']));
        Schema::table('bao_cao_doanh_thu_nhan_vien', fn(Blueprint $t) => $t->dropUnique(['bao_cao_doanh_thu_nhan_vien_ngay_nhan_vien_id_unique']));
        Schema::table('bao_cao_doanh_thu_suat', fn(Blueprint $t) => $t->dropUnique(['bao_cao_doanh_thu_suat_suat_chieu_id_unique']));
    }
};
