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
            ['ten' => 'Tiếng Hàn'],
        ]);
    }
}
