<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed các bảng cơ bản
        $this->call([
            VaiTroSeeder::class,
            AdminSeeder::class,
            NgonNguSeeder::class,
            DinhDangSeeder::class,
            DanhMucSeeder::class,
            RapSeeder::class,
            OrdersSeeder::class,
            ChatHistorySeeder::class,
            MovieSeeder::class,
            ShowtimeSeeder::class,
            ComboSeeder::class,
             // ✅ Thêm seeder lịch sử chat
        ]);

        // Tạo user mẫu bằng factory
        $users = User::factory(10)->create();

        // Gán vai trò 'khach_hang' nếu có
        $khach = VaiTro::where('ten', 'khach_hang')->first();
        if ($khach) {
            foreach ($users as $u) {
                $u->vai_tro_id = $khach->id;
                $u->save();
            }
        }

        // Tạo admin nếu chưa có
        $manager = VaiTro::where('ten', 'quan_ly')->first();
        if ($manager) {
            User::firstOrCreate([
                'email' => 'admin@gmail.com'
            ], [
                'ho_ten' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'),
                'vai_tro_id' => $manager->id,
            ]);
        }
    }
}
