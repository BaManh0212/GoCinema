<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RapSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rap')->insert([
            [
                'ten' => 'Gocinema',
                'logo' => 'logo-datn.png',
                'dia_chi' => '13 Trịnh Văn Bô, Hà Nội',
                'so_dien_thoai' => '0359445669',
                'email' => 'gocinema@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
