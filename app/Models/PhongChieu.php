<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhongChieu extends Model
{
    use HasFactory;

    protected $table = 'phong_chieu';

    protected $fillable = [
        'rap_id',
        'ten',
        'tong_ghe',
        'so_do',
        'so_hang',
        'so_cot',
        'dinh_dang_id',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
        'ngay_xoa',
    ];

    public $timestamps = false;

    // Phòng chiếu thuộc về một rạp
    public function rap()
    {
        return $this->belongsTo(Rap::class, 'rap_id');
    }

    // Phòng chiếu có định dạng (2D, 3D, IMAX,...)
    public function dinhDang()
    {
        return $this->belongsTo(DinhDang::class, 'dinh_dang_id');
    }

    // Ghế của phòng
    public function ghes()
    {
        return $this->hasMany(Ghe::class, 'phong_id');
    }

    // Sơ đồ ghế của phòng (mỗi phòng 1 sơ đồ)
    public function soDoGhe()
    {
        return $this->hasOne(SoDoGhe::class, 'phong_id');
    }

    // Suất chiếu trong phòng
    public function suatChieu()
    {
        return $this->hasMany(SuatChieu::class, 'phong_id');
    }
}
