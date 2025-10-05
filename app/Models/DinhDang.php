<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DinhDang extends Model
{
    use HasFactory;

    protected $table = 'dinh_dang';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];
}
