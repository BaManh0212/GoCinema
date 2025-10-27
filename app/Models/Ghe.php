<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ghe extends Model
{
    use HasFactory;

    protected $table = 'ghe';

    protected $fillable = [
        'phong_id',
        'hang',
        'cot',
        'loai',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
        'ngay_xoa'
    ];

    public $timestamps = false;

    public function phong()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }
}
