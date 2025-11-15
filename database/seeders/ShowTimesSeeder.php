<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showtime;
use Carbon\Carbon;

class ShowtimeSeeder extends Seeder
{
    public function run()
    {
        // Hôm nay
        Showtime::create([
            'movie_id' => 1,
            'start_time' => Carbon::today()->setHour(19)->setMinute(0),
            'price' => 90000,
            'room' => 'Phòng 1'
        ]);

        Showtime::create([
            'movie_id' => 2,
            'start_time' => Carbon::today()->setHour(21)->setMinute(30),
            'price' => 100000,
            'room' => 'Phòng 2'
        ]);

        // Ngày mai
        Showtime::create([
            'movie_id' => 3,
            'start_time' => Carbon::tomorrow()->setHour(18)->setMinute(0),
            'price' => 95000,
            'room' => 'Phòng 3'
        ]);

        Showtime::create([
            'movie_id' => 1,
            'start_time' => Carbon::tomorrow()->setHour(20)->setMinute(30),
            'price' => 105000,
            'room' => 'Phòng 4'
        ]);
    }
}
