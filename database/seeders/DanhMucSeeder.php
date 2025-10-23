<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\DanhMuc;

class DanhMucSeeder extends Seeder
{
    public function run()
    {
        $danhmucs = [
            'Hành động',
            'Kinh dị',
            'Hài hước',
            'Tình cảm',
            'Hoạt hình',
        ];

        foreach ($danhmucs as $ten) {
            DanhMuc::create([
                'ten' => $ten,
                'slug' => Str::slug($ten),
            ]);
        }
    }
}
