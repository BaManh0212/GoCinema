<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaGiamGia extends Model
{
    use HasFactory;

    protected $table = 'ma_giam_gia';

    protected $fillable = [
        'ma',
        'loai',
        'gia_tri',
        'gia_tri_don_hang_toi_thieu',
        'ap_dung_cho',
        'so_diem_can_thiet',
        'so_lan_su_dung',
        'kich_hoat',
        'ngay_bat_dau',
        'ngay_ket_thuc',
    ];

    protected $casts = [
        'gia_tri' => 'decimal:2',
        'gia_tri_don_hang_toi_thieu' => 'decimal:2',
        'so_diem_can_thiet' => 'integer',
        'so_lan_su_dung' => 'integer',
        'kich_hoat' => 'boolean',
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
    ];

    /**
     * Relationships
     */
    public function donDatVe()
    {
        return $this->hasMany(DonDatVe::class, 'ma_giam_gia_id');
    }

    /**
     * Scopes
     */
    public function scopeKichHoat($query)
    {
        return $query->where('kich_hoat', true)
                    ->where('ngay_bat_dau', '<=', now())
                    ->where('ngay_ket_thuc', '>=', now());
    }

    /**
     * Helper methods
     */
    public function tinhGiamGia($tongTien)
    {
        if ($this->loai === 'phan_tram') {
            return $tongTien * ($this->gia_tri / 100);
        }
        return $this->gia_tri;
    }
}
