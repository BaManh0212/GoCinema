<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ghe extends Model
{
    use HasFactory;

    protected $table = 'ghe'; // tên bảng

    protected $fillable = [
        'phong_id',
        'hang',
        'cot',
        'loai',
        'trang_thai',
    ];

    // Quan hệ với phòng chiếu
    public function phongChieu()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id', 'id');
    }
}
