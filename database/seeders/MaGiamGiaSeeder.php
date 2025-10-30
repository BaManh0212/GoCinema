<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaGiamGiaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'ma' => 'SALE10',
                'loai' => 'phan_tram',
                'gia_tri' => 10,
                'giam_toi_da' => 50000,
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 'tat_ca',
                'so_luong' => 100,
                'so_lan_su_dung' => 1,
                'kich_hoat' => true,
                'ngay_bat_dau' => $now,
                'ngay_ket_thuc' => $now->copy()->addDays(30),
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'ma' => 'GIAM50K',
                'loai' => 'so_tien',
                'gia_tri' => 50000,
                'giam_toi_da' => null,
                'gia_tri_don_hang_toi_thieu' => 200000,
                'ap_dung_cho' => 'san_pham',
                'so_luong' => 50,
                'so_lan_su_dung' => 1,
                'kich_hoat' => true,
                'ngay_bat_dau' => $now,
                'ngay_ket_thuc' => $now->copy()->addDays(15),
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'ma' => 'MOVIE20',
                'loai' => 'phan_tram',
                'gia_tri' => 20,
                'giam_toi_da' => 30000,
                'gia_tri_don_hang_toi_thieu' => 80000,
                'ap_dung_cho' => 've',
                'so_luong' => 200,
                'so_lan_su_dung' => 1,
                'kich_hoat' => true,
                'ngay_bat_dau' => $now,
                'ngay_ket_thuc' => $now->copy()->addDays(20),
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        DB::table('ma_giam_gia')->insert($data);
    }
}
