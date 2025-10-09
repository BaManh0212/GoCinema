<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use App\Models\VaiTro;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(VaiTroSeeder::class);

        // Create some users using the User factory (Breeze)
        $users = User::factory(10)->create();
        // assign default role 'khach_hang' if exists
        $khach = VaiTro::where('ten', 'khach_hang')->first();
        if ($khach) {
            foreach ($users as $u) {
                $u->vai_tro_id = $khach->id;
                $u->save();
            }
        }

        // Ensure there's a manager user
        $manager = VaiTro::where('ten', 'quan_ly')->first();
        if ($manager) {
            User::firstOrCreate([
                'email' => 'admin@gmail.com'
            ], [
                'ho_ten' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => 'password',
                'vai_tro_id' => $manager->id,
            ]);
        }
    }
}
