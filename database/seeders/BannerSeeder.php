<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use Carbon\Carbon;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Banner Trang Chủ 1',
                'type' => 'image',
                'image' => 'uploads/banners/banner1.jpg',
                'link' => null,
                'description' => 'Banner quảng cáo phim mới',
                'is_active' => true,
                'display_order' => 1,
                'start_at' => Carbon::now()->subDays(10),
                'end_at' => Carbon::now()->addDays(30),
            ],
            [
                'title' => 'Banner Trang Chủ 2',
                'type' => 'image',
                'image' => 'uploads/banners/banner2.jpg',
                'link' => null,
                'description' => 'Banner khuyến mãi',
                'is_active' => true,
                'display_order' => 2,
                'start_at' => Carbon::now()->subDays(10),
                'end_at' => Carbon::now()->addDays(30),
            ],
            [
                'title' => 'Banner Trang Chủ 3',
                'type' => 'image',
                'image' => 'uploads/banners/banner3.jpg',
                'link' => null,
                'description' => 'Banner sự kiện đặc biệt',
                'is_active' => true,
                'display_order' => 3,
                'start_at' => Carbon::now()->subDays(10),
                'end_at' => Carbon::now()->addDays(30),
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        $this->command->info('✅ Đã tạo ' . count($banners) . ' banners mẫu!');
    }
}
