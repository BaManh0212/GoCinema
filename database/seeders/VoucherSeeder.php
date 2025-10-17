<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CHỈ TẠO VOUCHER DÀNH CHO VÉ PHIM
        $vouchers = [
            [
                'ten' => 'Giảm 50% giá vé phim',
                'loai' => 'phan_tram',
                'gia_tri' => 50,
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 500,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 30.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 30000,
                'gia_tri_don_hang_toi_thieu' => 80000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 300,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 20.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 20000,
                'gia_tri_don_hang_toi_thieu' => 50000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 200,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 20% giá vé phim',
                'loai' => 'phan_tram',
                'gia_tri' => 20,
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 1000,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 15.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 15000,
                'gia_tri_don_hang_toi_thieu' => 50000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 150,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonth(),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 100.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 100000,
                'gia_tri_don_hang_toi_thieu' => 200000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 800,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 40% giá vé phim',
                'loai' => 'phan_tram',
                'gia_tri' => 40,
                'gia_tri_don_hang_toi_thieu' => 150000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 400,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
            [
                'ten' => 'Giảm 50.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 50000,
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 600,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }

        $this->command->info('✅ Đã tạo ' . count($vouchers) . ' voucher DÀNH CHỈ CHO VÉ PHIM!');
    }
}
