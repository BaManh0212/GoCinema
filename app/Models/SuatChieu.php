<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuatChieu extends Model
{
    use HasFactory;

    protected $table = 'suat_chieu';

    protected $fillable = [
        'phim_id',
        'phong_id',
        'gio_bat_dau',
        'gio_ket_thuc',
        'gia_ve',
    ];

    public $timestamps = false; // vì bạn đang dùng các cột ngày riêng (ngay_tao, ngay_cap_nhat, ...)

    // Quan hệ
    public function phim()
    {
        return $this->belongsTo(Phim::class, 'phim_id');
    }

    public function phong()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }

    // backward-compatible alias: some views/controllers expect phongChieu relation
    public function phongChieu()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }
}
