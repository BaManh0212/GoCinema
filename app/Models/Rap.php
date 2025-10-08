<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rap extends Model
{
    use HasFactory;

    protected $table = 'rap'; // Tên bảng trong database

    protected $fillable = [
        'ten',
        'dia_chi'
    ];
}
