<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function suatChieu(): BelongsTo
    {
        return $this->belongsTo(SuatChieu::class, 'suat_chieu_id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function chiTietVes(): HasMany
    {
        return $this->hasMany(ChiTietVe::class, 'don_dat_ve_id');
    }
}
