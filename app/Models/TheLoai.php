<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheLoai extends Model
{
    use HasFactory;

    protected $table = 'the_loai'; // 👈 tên bảng thật trong DB, ví dụ 'the_loai' hoặc 'theloai'

    protected $fillable = [
        'ten',
        'mo_ta',
    ];
}
