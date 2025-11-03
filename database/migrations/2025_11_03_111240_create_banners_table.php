<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Loại banner: ảnh hoặc video
            $table->enum('type', ['image', 'video'])->default('image');

            // Tiêu đề
            $table->string('title')->nullable();

            // Nếu là ảnh → lưu đường dẫn ảnh
            $table->string('image')->nullable();

            // Nếu là video → lưu đường dẫn file video (nếu upload) hoặc link youtube/vimeo
            $table->string('video_url')->nullable();

            // Link chuyển hướng khi click banner
            $table->string('link')->nullable();

            // Thứ tự hiển thị
            $table->integer('display_order')->default(0);

            // Trạng thái hiển thị
            $table->boolean('is_active')->default(true);

            // Thời gian hiển thị
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
