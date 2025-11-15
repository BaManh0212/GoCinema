<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    public function run()
    {
        Movie::create([
            'title' => 'Inside Out 2',
            'genre' => 'Hoạt hình',
            'duration' => 120,
            'description' => 'Câu chuyện tiếp nối về những cảm xúc trong đầu cô bé Riley.'
        ]);

        Movie::create([
            'title' => 'Avengers: Endgame',
            'genre' => 'Hành động',
            'duration' => 180,
            'description' => 'Trận chiến cuối cùng của các siêu anh hùng chống lại Thanos.'
        ]);

        Movie::create([
            'title' => 'Con Nhót Mót Chồng',
            'genre' => 'Hài tình cảm',
            'duration' => 110,
            'description' => 'Một bộ phim Việt Nam đầy cảm xúc và hài hước.'
        ]);
    }
}
