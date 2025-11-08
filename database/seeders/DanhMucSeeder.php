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
            [
                'ten' => 'Hành động',
                'mo_ta' => 'Phim với những pha hành động kịch tính, chiến đấu và mạo hiểm.',
            ],
            [
                'ten' => 'Kinh dị',
                'mo_ta' => 'Phim rùng rợn, giật gân và đầy hồi hộp.',
            ],
            [
                'ten' => 'Hài hước',
                'mo_ta' => 'Phim mang đến những tràng cười sảng khoái, giải trí nhẹ nhàng.',
            ],
            [
                'ten' => 'Tình cảm',
                'mo_ta' => 'Phim kể về chuyện tình cảm lãng mạn, xúc động và cảm động.',
            ],
            [
                'ten' => 'Hoạt hình',
                'mo_ta' => 'Phim hoạt hình vui nhộn, sáng tạo, thích hợp cho mọi lứa tuổi.',
            ],
            [
                'ten' => 'Pháp sư',
                'mo_ta' => 'Pháp sư Việt Nam',
            ],
            [
                'ten' => 'Ma thuật',
                'mo_ta' => 'Sức mạnh siêu nhiên',
            ],
            [
                'ten' => 'Tâm lý',
                'mo_ta' => 'Phim tâm lý sâu sắc, khám phá tâm trí con người.',
            ],
            [
                'ten' => 'Phim gia đình',
                'mo_ta' => 'Phim gia đình ấm áp, ý nghĩa, thích hợp cho mọi lứa tuổi.',
            ],
            [
                'ten' => 'Viễn Tây',
                'mo_ta' => 'Phim Viễn Tây với những cuộc phiêu lưu mạo hiểm, cao bồi và bắn súng.',
            ],
            [
                'ten' => 'Chiến tranh',
                'mo_ta' => 'Phim chiến tranh với những trận đánh khốc liệt, tâm lý nhân vật sâu sắc.',
            ],
            [
                'ten' => 'Ca nhạc',
                'mo_ta' => 'Phim ca nhạc với những bản nhạc hay, đi cùng câu chuyện cảm động.',
            ],
            [
                'ten' => 'Tài liệu',
                'mo_ta' => 'Phim tài liệu khám phá thế giới tự nhiên, văn hóa và con người.',
            ],
            [
                'ten' => 'Bí ẩn/ Trinh thám ',
                'mo_ta' => 'Phim có một bí ẩn cần được giải mã, thường là một vụ án mạng hoặc một sự kiện kỳ lạ.',
            ],
            [
                'ten' => 'Hình sự/Tội phạm',
                'mo_ta' => 'Xoay quanh các vụ án, cuộc đấu tranh giữa cảnh sát và tội phạm.',
            ],
            [
                'ten' => 'Thần thoại/Kỳ ảo',
                'mo_ta' => 'Thế giới phim có phép thuật, sinh vật thần thoại và các yếu tố siêu nhiên.',
            ],
            [
                'ten' => 'Phiêu lưu',
                'mo_ta' => 'Phim về những chuyến đi khám phá, mạo hiểm đến những vùng đất mới.',
            ],
            [
                'ten' => 'Khoa học viễn tưởng',
                'mo_ta' => 'Phim khám phá các khái niệm khoa học, công nghệ tương lai và vũ trụ.',
            ],
            [
                'ten' => 'Lãng mạn',
                'mo_ta' => 'Kể về câu chuyện tình yêu giữa các nhân vật.',
            ],
        ];

        foreach ($danhmucs as $danhmuc) {
            DanhMuc::create([
                'ten' => $danhmuc['ten'],
                'slug' => Str::slug($danhmuc['ten']),
                'mo_ta' => $danhmuc['mo_ta'],
            ]);
        }
    }
}
