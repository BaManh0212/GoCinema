<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use App\Models\VaiTro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo vai trò quản lý
        $vaiTro = VaiTro::firstOrCreate(
            ['ten' => 'quan_ly'],
            ['mo_ta' => 'Quản lý hệ thống']
        );

        // Tạo tài khoản admin
        NguoiDung::create([
            'ho_ten' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'vai_tro_id' => $vaiTro->id,
        ]);
    }
}
