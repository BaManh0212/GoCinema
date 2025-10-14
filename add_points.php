<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NguoiDung;

echo "=== THÊM ĐIỂM CHO USER ===\n\n";

try {
    // Lấy user admin
    $user = NguoiDung::where('email', 'admin@gocinema.vn')->first();
    
    if (!$user) {
        echo "❌ Không tìm thấy user admin@gocinema.vn\n";
        exit(1);
    }

    echo "User: {$user->email}\n";
    echo "Điểm hiện tại: {$user->diem}\n\n";

    // Thêm điểm
    $user->themDiem(200, 'Thưởng điểm khởi tạo hệ thống');
    echo "✅ Đã thêm 200 điểm\n";
    echo "Điểm mới: {$user->fresh()->diem}\n\n";

    // Thêm điểm cho các user khác
    $users = NguoiDung::whereIn('email', [
        'quanly@gocinema.vn',
        'nhanvien@gocinema.vn',
        'khachhang@gocinema.vn',
        'user@gocinema.vn'
    ])->get();

    foreach ($users as $u) {
        $diemThem = rand(100, 300);
        $u->themDiem($diemThem, 'Thưởng điểm khởi tạo hệ thống');
        echo "✅ {$u->email}: +{$diemThem} điểm (Tổng: {$u->fresh()->diem})\n";
    }

    echo "\n=== HOÀN THÀNH ===\n";

} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    exit(1);
}
