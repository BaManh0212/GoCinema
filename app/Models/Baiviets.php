<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baiviets extends Model
{
    use HasFactory;

    protected $table = 'baiviets'; // tên bảng trong database

    protected $fillable = [
        'tieu_de',
        'slug',
        'hinh_anh',
        'tom_tat',
        'noi_dung',
        'loai',
        'ngay_phat_hanh',
        'ngay_ket_thuc',
        'is_active',
        'is_featured',
        'is_promo',
        'views',
        'thoi_luong',
        'dao_dien',
        'dien_vien',
        'ngon_ngu',
        'dinh_dang',
        'phu_de',
        'gioi_han_tuoi',
    ];
}
