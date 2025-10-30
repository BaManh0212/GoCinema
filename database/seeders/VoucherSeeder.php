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
                'giam_toi_da' => 50000, // Giảm tối đa 50k
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 500,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 50, // Giới hạn 50 voucher
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 30.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 30000,
                'giam_toi_da' => null, // Voucher số tiền không cần giới hạn
                'gia_tri_don_hang_toi_thieu' => 80000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 300,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 100,
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 20.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 20000,
                'giam_toi_da' => null,
                'gia_tri_don_hang_toi_thieu' => 50000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 200,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 150,
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 20% giá vé phim',
                'loai' => 'phan_tram',
                'gia_tri' => 20,
                'giam_toi_da' => 30000, // Giảm tối đa 30k
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 1000,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 30,
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 15.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 15000,
                'giam_toi_da' => null,
                'gia_tri_don_hang_toi_thieu' => 50000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 150,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonth(),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 200,
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 100.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 100000,
                'giam_toi_da' => null,
                'gia_tri_don_hang_toi_thieu' => 200000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 800,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 20,
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 40% giá vé phim',
                'loai' => 'phan_tram',
                'gia_tri' => 40,
                'giam_toi_da' => 60000, // Giảm tối đa 60k
                'gia_tri_don_hang_toi_thieu' => 150000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 400,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 80,
                'so_luong_da_dung' => 0,
            ],
            [
                'ten' => 'Giảm 50.000đ cho vé phim',
                'loai' => 'so_tien',
                'gia_tri' => 50000,
                'giam_toi_da' => null,
                'gia_tri_don_hang_toi_thieu' => 100000,
                'ap_dung_cho' => 've', // CHỈ VÉ
                'diem_can' => 600,
                'kich_hoat' => true,
                'ngay_bat_dau' => Carbon::now(),
                'ngay_ket_thuc' => Carbon::now()->addMonths(6),
                'so_lan_su_dung' => 1,
                'so_luong_toi_da' => 60,
                'so_luong_da_dung' => 0,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }

        $this->command->info('✅ Đã tạo ' . count($vouchers) . ' voucher DÀNH CHỈ CHO VÉ PHIM!');
    }
}
