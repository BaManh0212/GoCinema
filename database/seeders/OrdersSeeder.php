<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\NguoiDung;
use App\Models\Rap;
use App\Models\PhongChieu;
use App\Models\Ghe;
use App\Models\Phim;
use App\Models\SuatChieu;
use App\Models\DonDatVe;
use App\Models\ChiTietVe;
use Illuminate\Support\Facades\DB;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo vài người dùng test nếu chưa có
        $users = NguoiDung::take(5)->get();
        if ($users->count() < 5) {
            for ($i = 1; $i <= 5; $i++) {
                $users[] = NguoiDung::create([
                    'ho_ten' => "Khách Test $i",
                    'email' => "test{$i}@example.com",
                    'password' => 'password',
                    'so_dien_thoai' => '0123456789',
                ]);
            }
        }

        // Lấy hoặc tạo rap + phòng chiếu test
        $rap = Rap::first() ?? Rap::create([
            'ten' => 'Rap Test',
            'dia_chi' => 'Địa chỉ test',
            'so_dien_thoai' => '0123456789',
            'email' => 'rap@test.local',
        ]);

        $phong = PhongChieu::firstWhere('ten', 'Phòng Test') ?: PhongChieu::create([
            'rap_id' => $rap->id,
            'ten' => 'Phòng Test',
            'tong_ghe' => 30,
            'so_do' => null,
            'dinh_dang_id' => DB::table('dinh_dang')->value('id') ?? 1,
            'trang_thai' => 'hoat_dong',
        ]);

        // Tạo ghế cho phòng nếu chưa có
        $gheCount = Ghe::where('phong_id', $phong->id)->count();
        if ($gheCount < 10) {
            for ($r = 0; $r < 2; $r++) {
                $hang = chr(65 + $r); // A, B
                for ($c = 1; $c <= 10; $c++) {
                    Ghe::firstOrCreate([
                        'phong_id' => $phong->id,
                        'hang' => $hang,
                        'cot' => $c,
                    ], [
                        'loai' => 'thuong',
                        'trang_thai' => 'hoat_dong',
                    ]);
                }
            }
        }

        // Tạo vài suất chiếu (dùng phim đã seed nếu có)
        $phims = Phim::take(5)->get();
        if ($phims->isEmpty()) {
            $phims[] = Phim::create([
                'tieu_de' => 'Phim Test',
                'slug' => Str::slug('Phim Test'),
                'trang_thai' => 1,
            ]);
        }

        $suatChieuIds = [];
        foreach ($phims as $index => $phim) {
            $start = now()->addDays($index)->setHour(14)->setMinute(0)->setSecond(0);
            $suat = SuatChieu::create([
                'phim_id' => $phim->id,
                'phong_id' => $phong->id,
                'gio_bat_dau' => $start,
                'gio_ket_thuc' => $start->copy()->addHours(2),
                'gia_ve' => 80000 + ($index * 5000),
            ]);
            $suatChieuIds[] = $suat->id;
        }

        // Tạo 5 đơn đặt vé, mỗi đơn 2 vé
        $geList = Ghe::where('phong_id', $phong->id)->get()->values();
        $possibleOrderStatuses = ['cho_thanh_toan', 'da_thanh_toan', 'da_checkin', 'da_huy'];
        for ($i = 0; $i < 5; $i++) {
            $user = $users[$i % count($users)];
            $suatId = $suatChieuIds[$i % count($suatChieuIds)];
            $gia = SuatChieu::find($suatId)->gia_ve ?? 80000;

            // pick a status with some weighting (more cho_thanh_toan, some paid, some checked-in)
            $status = $possibleOrderStatuses[array_rand($possibleOrderStatuses)];
            // prefer cho_thanh_toan more often
            if (rand(1, 100) <= 50) {
                $status = 'cho_thanh_toan';
            } elseif (rand(1, 100) <= 80) {
                $status = 'da_thanh_toan';
            }

            $don = DonDatVe::create([
                'ma_don' => strtoupper(Str::random(8)),
                'nguoi_dung_id' => $user->id,
                'suat_chieu_id' => $suatId,
                'ma_giam_gia_id' => null,
                'tong_tien' => $gia * 2,
                'trang_thai' => $status,
            ]);

            // Tạo 2 chi tiết vé
            $seat1 = $geList->shift() ?? $geList->first();
            $seat2 = $geList->shift() ?? $geList->first();

            // set ChiTietVe statuses consistent with order status
            $ctStatus = 'cho_thanh_toan';
            if ($status === 'da_thanh_toan') {
                $ctStatus = 'da_thanh_toan';
            } elseif ($status === 'da_checkin') {
                $ctStatus = 'da_su_dung';
            } elseif ($status === 'da_huy') {
                $ctStatus = 'da_huy';
            }

            if ($seat1) {
                ChiTietVe::create([
                    'don_dat_ve_id' => $don->id,
                    'suat_chieu_id' => $suatId,
                    'ghe_id' => $seat1->id,
                    'gia' => $gia,
                    'loai_ghe' => $seat1->loai ?? 'thuong',
                    'trang_thai' => $ctStatus,
                ]);
            }
            if ($seat2) {
                ChiTietVe::create([
                    'don_dat_ve_id' => $don->id,
                    'suat_chieu_id' => $suatId,
                    'ghe_id' => $seat2->id,
                    'gia' => $gia,
                    'loai_ghe' => $seat2->loai ?? 'thuong',
                    'trang_thai' => $ctStatus,
                ]);
            }
        }
    }
}
