<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GheSeeder extends Seeder
{
    public function run(): void
    {
        $gheData = [];
        $loaiList = ['thuong', 'vip', 'doi'];
        $trangThaiList = ['hoat_dong', 'hong', 'bao_tri'];
        $hangList = ['A', 'B'];
        $phongList = range(21, 30); // id phòng chiếu từ 21 → 30

        $now = Carbon::now();

        for ($i = 1; $i <= 10; $i++) {
            $gheData[] = [
                'phong_id'   => $phongList[array_rand($phongList)],
                'hang'       => $hangList[array_rand($hangList)],
                'cot'        => $i,
                'loai'       => $loaiList[array_rand($loaiList)],
                'trang_thai' => $trangThaiList[array_rand($trangThaiList)],
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        DB::table('ghe')->insert($gheData);
    }
}
