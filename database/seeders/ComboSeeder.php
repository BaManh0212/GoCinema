<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Combo;

class ComboSeeder extends Seeder
{
    public function run()
    {
        Combo::create([
            'name' => 'Combo 1',
            'price' => 50000,
            'description' => '1 bắp rang + 1 nước ngọt'
        ]);

        Combo::create([
            'name' => 'Combo 2',
            'price' => 80000,
            'description' => '2 bắp rang + 2 nước ngọt'
        ]);

        Combo::create([
            'name' => 'Combo Gia Đình',
            'price' => 150000,
            'description' => '4 bắp rang + 4 nước ngọt + 1 snack'
        ]);
    }
}
