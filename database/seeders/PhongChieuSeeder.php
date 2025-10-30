<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhongChieuSeeder extends Seeder
{
    public function run(): void
    {
         // Tạo rạp mặc định nếu chưa có
    if (!DB::table('rap')->where('id', 1)->exists()) {
        DB::table('rap')->insert([
            'id' => 1,
            'ten' => 'Rạp CGV GoCinema',
            'dia_chi' => '123 Đường 3/2, TP.HCM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
        $now = Carbon::now();

        $phongChieus = [
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 1 - IMAX',
                'tong_ghe' => 150,
                'so_do' => 'A1-A15, B1-B15, ...',
                'dinh_dang_id' => 1,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 2 - 3D',
                'tong_ghe' => 120,
                'so_do' => 'A1-A12, B1-B12, ...',
                'dinh_dang_id' => 2,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 3 - 2D',
                'tong_ghe' => 100,
                'so_do' => 'A1-A10, B1-B10, ...',
                'dinh_dang_id' => 3,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 4 - Dolby Atmos',
                'tong_ghe' => 180,
                'so_do' => 'A1-A18, B1-B18, ...',
                'dinh_dang_id' => 1,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 5 - 4DX',
                'tong_ghe' => 160,
                'so_do' => 'A1-A16, B1-B16, ...',
                'dinh_dang_id' => 2,
                'trang_thai' => 'bao_tri',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 6 - VIP',
                'tong_ghe' => 80,
                'so_do' => 'A1-A8, B1-B8, ...',
                'dinh_dang_id' => 1,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 7 - Standard',
                'tong_ghe' => 130,
                'so_do' => 'A1-A13, B1-B13, ...',
                'dinh_dang_id' => 3,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 8 - IMAX 3D',
                'tong_ghe' => 200,
                'so_do' => 'A1-A20, B1-B20, ...',
                'dinh_dang_id' => 2,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 9 - Family',
                'tong_ghe' => 90,
                'so_do' => 'A1-A9, B1-B9, ...',
                'dinh_dang_id' => 1,
                'trang_thai' => 'ngung_su_dung',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'rap_id' => 1,
                'ten' => 'Phòng Chiếu 10 - Private',
                'tong_ghe' => 60,
                'so_do' => 'A1-A6, B1-B6, ...',
                'dinh_dang_id' => 3,
                'trang_thai' => 'hoat_dong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('phong_chieu')->insert($phongChieus);
    }
}
