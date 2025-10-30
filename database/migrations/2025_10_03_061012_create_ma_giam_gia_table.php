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
        Schema::create('ma_giam_gia', function (Blueprint $table) {
            $table->id();

            // Mã code (duy nhất)
            $table->string('ma')->unique();

            // Loại giảm giá: phần trăm hoặc số tiền cố định
            $table->enum('loai', ['phan_tram', 'so_tien']);

            // Giá trị giảm (VD: 10% hoặc 50000)
            $table->decimal('gia_tri', 12, 2);

            // Giới hạn giảm tối đa (chỉ áp dụng nếu là loại phần trăm)
            $table->decimal('giam_toi_da', 12, 2)->nullable();

            // Giá trị đơn hàng tối thiểu để được giảm
            $table->decimal('gia_tri_don_hang_toi_thieu', 12, 2)->nullable();

            // Áp dụng cho loại sản phẩm nào
            $table->enum('ap_dung_cho', ['ve', 'san_pham', 'tat_ca'])->default('tat_ca');

            // Số lượng mã có thể sử dụng (tổng cộng)
            $table->integer('so_luong')->default(0);

            // Số lần 1 người dùng có thể sử dụng (nếu cần)
            $table->integer('so_lan_su_dung')->default(1);

            // Trạng thái
            $table->boolean('kich_hoat')->default(true);

            // Thời gian hiệu lực
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Nếu muốn hỗ trợ xóa mềm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ma_giam_gia');
    }
};
