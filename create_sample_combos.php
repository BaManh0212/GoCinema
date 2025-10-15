<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Combo;
use App\Models\ComboChiTiet;
use App\Models\SanPham;
use Illuminate\Support\Facades\DB;

echo "=== TẠO COMBO MẪU ===\n\n";

try {
    // Tạo sản phẩm mẫu nếu chưa có
    $sanPham = [];
    
    $sp1 = SanPham::updateOrCreate(
        ['ten' => 'Bắp rang bơ lớn'],
        [
            'gia' => 50000,
            'mo_ta' => 'Bắp rang bơ size lớn',
            'hinh_anh' => 'popcorn-large.jpg',
            'trang_thai' => 1
        ]
    );
    echo "✅ Sản phẩm: {$sp1->ten}\n";
    
    $sp2 = SanPham::updateOrCreate(
        ['ten' => 'Coca Cola lớn'],
        [
            'gia' => 30000,
            'mo_ta' => 'Nước ngọt Coca Cola size lớn',
            'hinh_anh' => 'coca-large.jpg',
            'trang_thai' => 1
        ]
    );
    echo "✅ Sản phẩm: {$sp2->ten}\n";
    
    $sp3 = SanPham::updateOrCreate(
        ['ten' => 'Hotdog'],
        [
            'gia' => 35000,
            'mo_ta' => 'Hotdog xúc xích',
            'hinh_anh' => 'hotdog.jpg',
            'trang_thai' => 1
        ]
    );
    echo "✅ Sản phẩm: {$sp3->ten}\n";
    
    $sp4 = SanPham::updateOrCreate(
        ['ten' => 'Nước suối'],
        [
            'gia' => 15000,
            'mo_ta' => 'Nước suối Aquafina',
            'hinh_anh' => 'water.jpg',
            'trang_thai' => 1
        ]
    );
    echo "✅ Sản phẩm: {$sp4->ten}\n\n";

    // Tạo các combo
    echo "=== TẠO COMBO ===\n\n";
    
    // Combo 1: Sinh viên (80k = 80 điểm)
    $combo1 = Combo::updateOrCreate(
        ['ten' => 'Combo Sinh Viên'],
        [
            'gia' => 80000,
            'mo_ta' => 'Combo tiết kiệm cho sinh viên - 1 bắp + 1 nước',
        ]
    );
    
    // Xóa chi tiết cũ và tạo mới
    ComboChiTiet::where('combo_id', $combo1->id)->delete();
    ComboChiTiet::create([
        'combo_id' => $combo1->id,
        'san_pham_id' => $sp1->id,
        'so_luong' => 1
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo1->id,
        'san_pham_id' => $sp2->id,
        'so_luong' => 1
    ]);
    echo "✅ {$combo1->ten} - {$combo1->gia}đ (80 điểm)\n";

    // Combo 2: Couple (150k = 150 điểm)
    $combo2 = Combo::updateOrCreate(
        ['ten' => 'Combo Couple'],
        [
            'gia' => 150000,
            'mo_ta' => 'Combo dành cho 2 người - 2 bắp + 2 nước + 2 hotdog',
        ]
    );
    
    ComboChiTiet::where('combo_id', $combo2->id)->delete();
    ComboChiTiet::create([
        'combo_id' => $combo2->id,
        'san_pham_id' => $sp1->id,
        'so_luong' => 2
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo2->id,
        'san_pham_id' => $sp2->id,
        'so_luong' => 2
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo2->id,
        'san_pham_id' => $sp3->id,
        'so_luong' => 2
    ]);
    echo "✅ {$combo2->ten} - {$combo2->gia}đ (150 điểm)\n";

    // Combo 3: Gia đình (250k = 250 điểm)
    $combo3 = Combo::updateOrCreate(
        ['ten' => 'Combo Gia Đình'],
        [
            'gia' => 250000,
            'mo_ta' => 'Combo cho cả gia đình - 3 bắp + 3 nước + 3 hotdog',
        ]
    );
    
    ComboChiTiet::where('combo_id', $combo3->id)->delete();
    ComboChiTiet::create([
        'combo_id' => $combo3->id,
        'san_pham_id' => $sp1->id,
        'so_luong' => 3
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo3->id,
        'san_pham_id' => $sp2->id,
        'so_luong' => 3
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo3->id,
        'san_pham_id' => $sp3->id,
        'so_luong' => 3
    ]);
    echo "✅ {$combo3->ten} - {$combo3->gia}đ (250 điểm)\n";

    // Combo 4: Tiết kiệm (50k = 50 điểm)
    $combo4 = Combo::updateOrCreate(
        ['ten' => 'Combo Tiết Kiệm'],
        [
            'gia' => 50000,
            'mo_ta' => 'Combo nhỏ gọn - 1 bắp nhỏ + 1 nước suối',
        ]
    );
    
    ComboChiTiet::where('combo_id', $combo4->id)->delete();
    ComboChiTiet::create([
        'combo_id' => $combo4->id,
        'san_pham_id' => $sp1->id,
        'so_luong' => 1
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo4->id,
        'san_pham_id' => $sp4->id,
        'so_luong' => 1
    ]);
    echo "✅ {$combo4->ten} - {$combo4->gia}đ (50 điểm)\n";

    // Combo 5: VIP (500k = 500 điểm)
    $combo5 = Combo::updateOrCreate(
        ['ten' => 'Combo VIP'],
        [
            'gia' => 500000,
            'mo_ta' => 'Combo cao cấp - 5 bắp + 5 nước + 5 hotdog',
        ]
    );
    
    ComboChiTiet::where('combo_id', $combo5->id)->delete();
    ComboChiTiet::create([
        'combo_id' => $combo5->id,
        'san_pham_id' => $sp1->id,
        'so_luong' => 5
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo5->id,
        'san_pham_id' => $sp2->id,
        'so_luong' => 5
    ]);
    ComboChiTiet::create([
        'combo_id' => $combo5->id,
        'san_pham_id' => $sp3->id,
        'so_luong' => 5
    ]);
    echo "✅ {$combo5->ten} - {$combo5->gia}đ (500 điểm)\n";

    echo "\n=== HOÀN THÀNH ===\n";
    echo "Đã tạo 4 sản phẩm và 5 combo thành công!\n\n";
    echo "Quy đổi: 1000đ = 1 điểm\n";
    echo "- Combo Tiết Kiệm: 50 điểm\n";
    echo "- Combo Sinh Viên: 80 điểm\n";
    echo "- Combo Couple: 150 điểm\n";
    echo "- Combo Gia Đình: 250 điểm\n";
    echo "- Combo VIP: 500 điểm\n";

} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    exit(1);
}
