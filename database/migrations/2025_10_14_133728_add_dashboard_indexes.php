<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ===== ĐƠN ĐẶT VÉ =====
        Schema::table('don_dat_ve', function (Blueprint $t) {
            // Lọc theo trạng thái + khoảng thời gian (dashboard)
            $t->index(['trang_thai','created_at'], 'ddv_status_created_idx');
            // Truy vấn theo suất chiếu trong một range thời gian
            $t->index(['suat_chieu_id','created_at'], 'ddv_suatchieu_created_idx');
            // Top khách hàng theo thời gian
            $t->index(['nguoi_dung_id','created_at'], 'ddv_user_created_idx');
            // Tổng quan theo ngày/tháng
            $t->index('created_at', 'ddv_created_idx');
        });

        // ===== CHI TIẾT VÉ =====
        Schema::table('chi_tiet_ve', function (Blueprint $t) {
            // Thống kê vé theo suất chiếu & trạng thái
            $t->index(['suat_chieu_id','trang_thai'], 'ctv_suatchieu_status_idx');
            // Vé theo trạng thái trong khoảng thời gian (heatmap giờ…)
            $t->index(['trang_thai','created_at'], 'ctv_status_created_idx');
            // Phân tích theo đơn + trạng thái
            $t->index(['don_dat_ve_id','trang_thai'], 'ctv_don_status_idx');
            // Dò vé đã sử dụng theo thời điểm quét
            $t->index('thoi_gian_su_dung', 'ctv_used_at_idx');
            $t->index('created_at', 'ctv_created_idx');
        });

        // ===== THANH TOÁN =====
        Schema::table('thanh_toan', function (Blueprint $t) {
            $t->index(['don_dat_ve_id','trang_thai'], 'tt_order_status_idx');
            $t->index(['trang_thai','created_at'], 'tt_status_created_idx');
            $t->index('ma_giao_dich', 'tt_magiaodich_idx');
        });

        // ===== SUẤT CHIẾU =====
        Schema::table('suat_chieu', function (Blueprint $t) {
            // Dùng cho thống kê theo phim/phòng theo ngày/giờ
            $t->index(['phim_id','gio_bat_dau'], 'sc_phim_batdau_idx');
            $t->index(['phong_id','gio_bat_dau'], 'sc_phong_batdau_idx');
            $t->index('gio_bat_dau', 'sc_batdau_idx');
            // Soft delete filter
            $t->index('deleted_at', 'sc_deleted_idx');
        });

        // ===== PHÒNG CHIẾU / GHẾ =====
        Schema::table('phong_chieu', function (Blueprint $t) {
            $t->index(['rap_id','deleted_at'], 'pc_rap_deleted_idx');
        });

        Schema::table('ghe', function (Blueprint $t) {
            $t->index('phong_id', 'ghe_phong_idx');
            // Khuyến nghị (nếu chưa có) để tránh trùng ghế trong 1 phòng:
            // $t->unique(['phong_id','hang','cot'], 'ghe_phong_hang_cot_unique');
        });

        // ===== COMBO / SẢN PHẨM TRONG ĐƠN =====
        Schema::table('don_dat_ve_combo', function (Blueprint $t) {
            $t->index('don_dat_ve_id', 'ddvc_order_idx');
        });

        Schema::table('don_hang_san_pham', function (Blueprint $t) {
            $t->index('don_dat_ve_id', 'dhsp_order_idx');
        });

        // ===== VOUCHER NGƯỜI DÙNG =====
        Schema::table('voucher_nguoi_dung', function (Blueprint $t) {
            // Tìm voucher còn hạn/còn dùng cho 1 user
            $t->index(['nguoi_dung_id','trang_thai','ngay_han'], 'vnd_user_status_exp_idx');
        });

        // ===== BÁO CÁO TỔNG HỢP =====
        Schema::table('bao_cao_doanh_thu_rap', function (Blueprint $t) {
            $t->index(['rap_id','ngay'], 'bcr_rap_ngay_idx');
        });
        Schema::table('bao_cao_doanh_thu_phim', function (Blueprint $t) {
            $t->index(['phim_id','ngay'], 'bcp_phim_ngay_idx');
        });
        Schema::table('bao_cao_doanh_thu_suat', function (Blueprint $t) {
            $t->index('suat_chieu_id', 'bcs_suatchieu_idx');
        });

        // ===== PHIM (tìm kiếm) =====
        Schema::table('phim', function (Blueprint $t) {
            $t->index('deleted_at', 'phim_deleted_idx');
        });
        // Fulltext (tuỳ chọn) cho tìm kiếm nhanh theo tiêu đề/đạo diễn/diễn viên
        try {
            DB::statement('ALTER TABLE phim ADD FULLTEXT INDEX phim_search_fts (tieu_de, dao_dien, dien_vien)');
        } catch (\Throwable $e) {
            // Bỏ qua nếu đã tồn tại hoặc engine không hỗ trợ
        }
    }

    public function down(): void
    {
        Schema::table('don_dat_ve', function (Blueprint $t) {
            $t->dropIndex('ddv_status_created_idx');
            $t->dropIndex('ddv_suatchieu_created_idx');
            $t->dropIndex('ddv_user_created_idx');
            $t->dropIndex('ddv_created_idx');
        });

        Schema::table('chi_tiet_ve', function (Blueprint $t) {
            $t->dropIndex('ctv_suatchieu_status_idx');
            $t->dropIndex('ctv_status_created_idx');
            $t->dropIndex('ctv_don_status_idx');
            $t->dropIndex('ctv_used_at_idx');
            $t->dropIndex('ctv_created_idx');
        });

        Schema::table('thanh_toan', function (Blueprint $t) {
            $t->dropIndex('tt_order_status_idx');
            $t->dropIndex('tt_status_created_idx');
            $t->dropIndex('tt_magiaodich_idx');
        });

        Schema::table('suat_chieu', function (Blueprint $t) {
            $t->dropIndex('sc_phim_batdau_idx');
            $t->dropIndex('sc_phong_batdau_idx');
            $t->dropIndex('sc_batdau_idx');
            $t->dropIndex('sc_deleted_idx');
        });

        Schema::table('phong_chieu', function (Blueprint $t) {
            $t->dropIndex('pc_rap_deleted_idx');
        });

        Schema::table('ghe', function (Blueprint $t) {
            $t->dropIndex('ghe_phong_idx');
            // $t->dropUnique('ghe_phong_hang_cot_unique');
        });

        Schema::table('don_dat_ve_combo', function (Blueprint $t) {
            $t->dropIndex('ddvc_order_idx');
        });

        Schema::table('don_hang_san_pham', function (Blueprint $t) {
            $t->dropIndex('dhsp_order_idx');
        });

        Schema::table('voucher_nguoi_dung', function (Blueprint $t) {
            $t->dropIndex('vnd_user_status_exp_idx');
        });

        Schema::table('bao_cao_doanh_thu_rap', function (Blueprint $t) {
            $t->dropIndex('bcr_rap_ngay_idx');
        });
        Schema::table('bao_cao_doanh_thu_phim', function (Blueprint $t) {
            $t->dropIndex('bcp_phim_ngay_idx');
        });
        Schema::table('bao_cao_doanh_thu_suat', function (Blueprint $t) {
            $t->dropIndex('bcs_suatchieu_idx');
        });

        Schema::table('phim', function (Blueprint $t) {
            $t->dropIndex('phim_deleted_idx');
        });
        try {
            DB::statement('ALTER TABLE phim DROP INDEX phim_search_fts');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
