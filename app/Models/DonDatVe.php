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

    protected $casts = [
        'tong_tien' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function suatChieu()
    {
        return $this->belongsTo(SuatChieu::class, 'suat_chieu_id');
    }

    public function maGiamGia()
    {
        return $this->belongsTo(MaGiamGia::class, 'ma_giam_gia_id');
    }

    public function chiTietVe()
    {
        return $this->hasMany(ChiTietVe::class, 'don_dat_ve_id');
    }

    public function donDatVeCombo()
    {
        return $this->hasMany(DonDatVeCombo::class, 'don_dat_ve_id');
    }

    public function thanhToan()
    {
        return $this->hasMany(ThanhToan::class, 'don_dat_ve_id');
    }

    /**
     * Scopes
     */
    public function scopeChoThanhToan($query)
    {
        return $query->where('trang_thai', 'cho_thanh_toan');
    }

    public function scopeDaThanhToan($query)
    {
        return $query->where('trang_thai', 'da_thanh_toan');
    }

    public function scopeDaHuy($query)
    {
        return $query->where('trang_thai', 'da_huy');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ma_don)) {
                $model->ma_don = 'DV' . time() . rand(1000, 9999);
            }
        });
    }
}
