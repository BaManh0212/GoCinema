<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use App\Models\VaiTro;
use App\Models\DinhDang;
use Illuminate\Support\Facades\Hash;
use App\Models\Phim;
use App\Models\PhongChieu;
use App\Models\Rap;
use App\Models\SuatChieu;
use App\Models\Ghe;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $vaiTro = VaiTro::query()->firstOrCreate([
                'ten' => 'khach_hang',
            ], [
                'mo_ta' => 'Khách hàng',
            ]);

            $dinhDang = DinhDang::query()->firstOrCreate([
                'ten' => '2D',
            ], [
                'mo_ta' => 'Định dạng 2D',
            ]);

            $user = NguoiDung::query()->firstOrCreate([
                'email' => 'khach@example.com',
            ], [
                'ho_ten' => 'Khách Demo',
                'mat_khau' => Hash::make('password'),
                'vai_tro_id' => $vaiTro->id,
                'kich_hoat' => true,
                'loai_tai_khoan' => 'khach_hang',
                'diem' => 0,
            ]);

            $rap = Rap::query()->firstOrCreate(['ten' => 'Rạp Trung Tâm'], ['dia_chi' => '123 ABC, Q1']);
            $phong = PhongChieu::query()->firstOrCreate([
                'rap_id' => $rap->id,
                'ten' => 'Phòng 1',
            ], [
                'tong_ghe' => 40,
                'so_do' => null,
                'dinh_dang_id' => $dinhDang->id,
                'trang_thai' => 'hoat_dong',
            ]);

            // Seats 5 rows x 8 cols
            foreach (['A','B','C','D','E'] as $row) {
                for ($c = 1; $c <= 8; $c++) {
                    Ghe::query()->firstOrCreate([
                        'phong_id' => $phong->id,
                        'hang' => $row,
                        'cot' => $c,
                    ], [
                        'loai' => $c >= 7 ? 'vip' : 'thuong',
                        'trang_thai' => 'hoat_dong',
                    ]);
                }
            }

            $movie = Phim::query()->firstOrCreate([
                'tieu_de' => 'Demo Movie',
            ], [
                'mo_ta' => 'Phim demo cho hệ thống đặt vé',
                'phu_de' => true,
                'thoi_luong' => 120,
                'ngay_cong_chieu' => now()->toDateString(),
            ]);

            SuatChieu::query()->firstOrCreate([
                'phim_id' => $movie->id,
                'phong_id' => $phong->id,
                'gio_bat_dau' => now()->addHour()->startOfMinute(),
                'gio_ket_thuc' => now()->addHours(3)->startOfMinute(),
            ], [
                'gia_ve' => 90000,
            ]);
        });
    }
}
