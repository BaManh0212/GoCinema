<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgonNguSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ngon_ngu')->insert([
            ['ten' => 'Tiếng Việt'],
            ['ten' => 'Tiếng Anh'],
            ['ten' => 'Tiếng Mỹ'],
            ['ten' => 'Tiếng Hàn'],
            ['ten' => 'Tiếng Nhật'],
            ['ten' => 'Tiếng Pháp'],
            ['ten' => 'Tiếng Đức'],
            ['ten' => 'Tiếng Tây Ban Nha'],
            ['ten' => 'Tiếng Trung Quốc'],
        ]);
    }
}
