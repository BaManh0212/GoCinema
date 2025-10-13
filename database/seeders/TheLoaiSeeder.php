<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TheLoaiSeeder extends Seeder
{
    public function run()
    {
        DB::table('the_loai')->insert([
            ['ten' => 'Hành động'],
            ['ten' => 'Kinh dị'],
            ['ten' => 'Hài hước'],
            ['ten' => 'Tình cảm'],
            ['ten' => 'Hoạt hình'],
        ]);
    }
}
