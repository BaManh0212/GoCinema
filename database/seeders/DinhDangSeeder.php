<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DinhDangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dinh_dang')->insert([
            ['ten' => '2D'],
            ['ten' => '3D'],
            ['ten' => 'IMAX'],
        ]);
    }
}
