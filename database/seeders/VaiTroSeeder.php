<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaiTro;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['ten' => 'khach_hang', 'mo_ta' => 'Khách hàng thường'],
            ['ten' => 'nhan_vien', 'mo_ta' => 'Nhân viên bán vé/ hỗ trợ'],
            ['ten' => 'quan_ly', 'mo_ta' => 'Quản lý hệ thống'],
        ];

        foreach ($roles as $r) {
            VaiTro::updateOrCreate(['ten' => $r['ten']], $r);
        }
    }
}
