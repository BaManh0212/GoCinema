<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatHistory;

class ChatHistorySeeder extends Seeder
{
    public function run(): void
    {
        ChatHistory::firstOrCreate([
            'user_message' => 'Phim nào đang chiếu hôm nay?',
            'bot_reply' => 'Hôm nay GoCinema đang chiếu: Inside Out 2, Tử Chiến Trên Không, Mục Sư Và Con Quỷ Âm Trì.'
        ]);

        ChatHistory::firstOrCreate([
            'user_message' => 'Giá vé bao nhiêu?',
            'bot_reply' => 'Giá vé từ 70.000đ đến 120.000đ tùy suất chiếu và loại ghế.'
        ]);
    }
}
