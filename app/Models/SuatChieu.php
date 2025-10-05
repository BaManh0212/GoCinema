<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuatChieu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suat_chieu';

    protected $fillable = [
        'phim_id',
        'phong_id',
        'gio_bat_dau',
        'gio_ket_thuc',
        'gia_ve',
    ];

    protected $casts = [
        'gio_bat_dau' => 'datetime',
        'gio_ket_thuc' => 'datetime',
        'gia_ve' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->belongsTo(Phim::class, 'phim_id');
    }

    public function phongChieu()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }

    public function donDatVe()
    {
        return $this->hasMany(DonDatVe::class, 'suat_chieu_id');
    }

    public function gheGiuTam()
    {
        return $this->hasMany(GheGiuTam::class, 'suat_chieu_id');
    }

    public function baoCaoDoanhThuSuat()
    {
        return $this->hasMany(BaoCaoDoanhThuSuat::class, 'suat_chieu_id');
    }

    /**
     * Scopes
     */
    public function scopeSapChieu($query)
    {
        return $query->where('gio_bat_dau', '>', now());
    }

    public function scopeDangChieu($query)
    {
        return $query->where('gio_bat_dau', '<=', now())
                    ->where('gio_ket_thuc', '>=', now());
    }

    public function scopeDaChieu($query)
    {
        return $query->where('gio_ket_thuc', '<', now());
    }

    /**
     * Helper methods
     */
    public function getGheConTrongAttribute()
    {
        $tongGhe = $this->phongChieu->tong_ghe;
        $gheDaDat = $this->donDatVe()
            ->whereIn('trang_thai', ['da_thanh_toan', 'dat_coc'])
            ->withCount('chiTietVe')
            ->get()
            ->sum('chi_tiet_ve_count');
        
        return $tongGhe - $gheDaDat;
    }
}
