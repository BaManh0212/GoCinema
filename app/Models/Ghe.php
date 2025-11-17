<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ghe extends Model
{
    use HasFactory;

    protected $table = 'ghe';

    protected $fillable = [
        'phong_id', 'hang', 'cot', 'loai', 'trang_thai'
    ];

}
