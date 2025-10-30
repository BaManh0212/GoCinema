<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhimSeeder extends Seeder
{
    public function run(): void
    {
        $phimData = [
            [
                'tieu_de' => 'Avengers: Endgame',
                'mo_ta' => 'Trận chiến cuối cùng của các siêu anh hùng chống lại Thanos.',
                'anh_poster' => 'images/phim1.jpg',
                'trailer' => 'https://youtu.be/TcMBFSGVi1c',
                'phu_de' => 1,
                'thoi_luong' => 181,
                'ngay_cong_chieu' => '2025-01-10',
                'do_tuoi_gioi_han' => 13,
                'danh_muc_id' => 1,
                'ngon_ngu_id' => 1,
            ],
            [
                'tieu_de' => 'Inception',
                'mo_ta' => 'Một kẻ trộm xâm nhập vào giấc mơ để đánh cắp ý tưởng.',
                'anh_poster' => 'images/phim2.jpg',
                'trailer' => 'https://youtu.be/YoHD9XEInc0',
                'phu_de' => 1,
                'thoi_luong' => 148,
                'ngay_cong_chieu' => '2025-02-15',
                'do_tuoi_gioi_han' => 16,
                'danh_muc_id' => 2,
                'ngon_ngu_id' => 1,
            ],
            [
                'tieu_de' => 'The Dark Knight',
                'mo_ta' => 'Batman đối đầu với Joker trong cuộc chiến bảo vệ Gotham.',
                'anh_poster' => 'images/phim3.jpg',
                'trailer' => 'https://youtu.be/EXeTwQWrcwY',
                'phu_de' => 0,
                'thoi_luong' => 152,
                'ngay_cong_chieu' => '2025-03-20',
                'do_tuoi_gioi_han' => 16,
                'danh_muc_id' => 2,
                'ngon_ngu_id' => 2,
            ],
            [
                'tieu_de' => 'Titanic',
                'mo_ta' => 'Chuyện tình huyền thoại trên con tàu định mệnh.',
                'anh_poster' => 'images/phim4.jpg',
                'trailer' => 'https://youtu.be/kVrqfYjkTdQ',
                'phu_de' => 1,
                'thoi_luong' => 195,
                'ngay_cong_chieu' => '2025-04-01',
                'do_tuoi_gioi_han' => 13,
                'danh_muc_id' => 3,
                'ngon_ngu_id' => 1,
            ],
            [
                'tieu_de' => 'Avatar',
                'mo_ta' => 'Cuộc phiêu lưu tại hành tinh Pandora huyền bí.',
                'anh_poster' => 'images/phim5.jpg',
                'trailer' => 'https://youtu.be/5PSNL1qE6VY',
                'phu_de' => 1,
                'thoi_luong' => 162,
                'ngay_cong_chieu' => '2025-05-10',
                'do_tuoi_gioi_han' => 13,
                'danh_muc_id' => 1,
                'ngon_ngu_id' => 3,
            ],
            [
                'tieu_de' => 'Joker',
                'mo_ta' => 'Hành trình trở thành ác nhân của Arthur Fleck.',
                'anh_poster' => 'images/phim6.jpg',
                'trailer' => 'https://youtu.be/zAGVQLHvwOY',
                'phu_de' => 0,
                'thoi_luong' => 122,
                'ngay_cong_chieu' => '2025-06-18',
                'do_tuoi_gioi_han' => 18,
                'danh_muc_id' => 2,
                'ngon_ngu_id' => 1,
            ],
            [
                'tieu_de' => 'Interstellar',
                'mo_ta' => 'Nhóm phi hành gia du hành xuyên không gian tìm hành tinh mới cho loài người.',
                'anh_poster' => 'images/phim7.jpg',
                'trailer' => 'https://youtu.be/zSWdZVtXT7E',
                'phu_de' => 1,
                'thoi_luong' => 169,
                'ngay_cong_chieu' => '2025-07-12',
                'do_tuoi_gioi_han' => 13,
                'danh_muc_id' => 3,
                'ngon_ngu_id' => 1,
            ],
            [
                'tieu_de' => 'The Godfather',
                'mo_ta' => 'Câu chuyện về gia đình mafia Corleone đầy quyền lực.',
                'anh_poster' => 'images/phim8.jpg',
                'trailer' => 'https://youtu.be/sY1S34973zA',
                'phu_de' => 0,
                'thoi_luong' => 175,
                'ngay_cong_chieu' => '2025-08-01',
                'do_tuoi_gioi_han' => 18,
                'danh_muc_id' => 2,
                'ngon_ngu_id' => 2,
            ],
            [
                'tieu_de' => 'Spider-Man: No Way Home',
                'mo_ta' => 'Peter Parker đối mặt với đa vũ trụ hỗn loạn.',
                'anh_poster' => 'images/phim9.jpg',
                'trailer' => 'https://youtu.be/JfVOs4VSpmA',
                'phu_de' => 1,
                'thoi_luong' => 148,
                'ngay_cong_chieu' => '2025-09-25',
                'do_tuoi_gioi_han' => 13,
                'danh_muc_id' => 1,
                'ngon_ngu_id' => 3,
            ],
            [
                'tieu_de' => 'Frozen II',
                'mo_ta' => 'Elsa và Anna khám phá nguồn gốc sức mạnh băng giá.',
                'anh_poster' => 'images/phim10.jpg',
                'trailer' => 'https://youtu.be/Zi4LMpSDccc',
                'phu_de' => 1,
                'thoi_luong' => 103,
                'ngay_cong_chieu' => '2025-10-01',
                'do_tuoi_gioi_han' => 0,
                'danh_muc_id' => 3,
                'ngon_ngu_id' => 2,
            ],
        ];

        foreach ($phimData as &$phim) {
            $phim['slug'] = Str::slug($phim['tieu_de']);
            $phim['created_at'] = now();
            $phim['updated_at'] = now();
        }

        DB::table('phim')->insert($phimData);
    }
}
