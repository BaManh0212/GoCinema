<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhMucSeeder extends Seeder
{
    public function run()
    {
        DB::table('danh_muc')->insert([
            ['ten' => 'Hành động'],
            ['ten' => 'Kinh dị'],
            ['ten' => 'Hài hước'],
            ['ten' => 'Tình cảm'],
            ['ten' => 'Hoạt hình'],
            ['ten' => 'Phiêu lưu'],
            ['ten' => 'Khoa học viễn tưởng'],
            ['ten' => 'Tài liệu'],
            ['ten' => 'Thể thao'],
            ['ten' => 'Âm nhạc'],
            ['ten' => 'Lịch sử'],
            ['ten' => 'Chiến tranh'],
            ['ten' => 'Tâm lý'],
            ['ten' => 'Gia đình'],
            ['ten' => 'Học đường'],
            ['ten' => 'Siêu nhiên'],
            ['ten' => 'Võ thuật'],
            ['ten' => 'Phim bộ'],
            ['ten' => 'Phim lẻ'],
        ]);
    }
}
