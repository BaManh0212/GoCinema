<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonDatVe extends Model
{
    use HasFactory;

    protected $table = 'don_dat_ve';

    protected $fillable = [
        'ma_don',
        'nguoi_dung_id',
        'suat_chieu_id',
        'ma_giam_gia_id',
        'tong_tien',
        'trang_thai',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function suatChieu()
    {
        return $this->belongsTo(SuatChieu::class, 'suat_chieu_id');
    }

    public function maGiamGia()
    {
        return $this->belongsTo(MaGiamGia::class, 'ma_giam_gia_id');
    }

    public function chiTietVes()
    {
        return $this->hasMany(ChiTietVe::class, 'don_dat_ve_id');
    }
}
