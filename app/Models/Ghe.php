<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ghe extends Model
{
    use HasFactory;

    protected $table = 'ghe';

    protected $fillable = [
        'so_do_id', 'ten', 'hang', 'cot', 'loai', 'trang_thai'
    ];

    public function soDo()
    {
        return $this->belongsTo(SoDoGhe::class, 'so_do_id');
    }
}
