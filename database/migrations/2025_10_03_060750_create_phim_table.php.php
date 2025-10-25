<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phim', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->string('slug')->unique(); // ➕ đường dẫn thân thiện SEO
            $table->text('mo_ta')->nullable();
            $table->string('anh_poster')->nullable(); // ảnh lớn
            $table->string('banner')->nullable(); // ➕ ảnh banner ngang
            $table->string('trailer')->nullable();
            $table->boolean('phu_de')->default(false);
            $table->integer('thoi_luong')->nullable();
            $table->date('ngay_cong_chieu')->nullable();
            $table->date('ngay_ket_thuc')->nullable(); // ➕ thời điểm kết thúc chiếu
            $table->string('dao_dien')->nullable(); // ➕ đạo diễn
            $table->text('dien_vien')->nullable(); // ➕ diễn viên
            $table->string('do_tuoi_gioi_han', 10)->nullable();
            $table->string('dinh_dang', 10)->default('2D'); // ➕ định dạng (2D, 3D,…)
            $table->tinyInteger('trang_thai')->default(1); // ➕ 1=Đang chiếu, 2=Sắp chiếu, 0=Ngừng
            $table->decimal('danh_gia', 3, 1)->default(0); // ➕ điểm đánh giá
            $table->integer('luot_xem')->default(0); // ➕ lượt xem / đặt vé
            $table->unsignedBigInteger('danh_muc_id')->nullable();
            $table->unsignedBigInteger('ngon_ngu_id')->nullable();

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at

            // 🔗 Khóa ngoại
            $table->foreign('danh_muc_id')->references('id')->on('danh_muc')->onDelete('set null');
            $table->foreign('ngon_ngu_id')->references('id')->on('ngon_ngu')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phim');
    }
};
