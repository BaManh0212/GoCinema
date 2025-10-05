<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';

    protected $fillable = [
        'don_dat_ve_id',
        'phuong_thuc_id',
        'so_tien',
        'ma_giao_dich',
        'trang_thai',
    ];

    protected $casts = [
        'so_tien' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function donDatVe()
    {
        return $this->belongsTo(DonDatVe::class, 'don_dat_ve_id');
    }

    public function phuongThucThanhToan()
    {
        return $this->belongsTo(PhuongThucThanhToan::class, 'phuong_thuc_id');
    }

    /**
     * Scopes
     */
    public function scopeThanhCong($query)
    {
        return $query->where('trang_thai', 'thanh_cong');
    }

    public function scopeDangXuLy($query)
    {
        return $query->where('trang_thai', 'dang_xu_ly');
    }

    public function scopeThatBai($query)
    {
        return $query->where('trang_thai', 'that_bai');
    }
}
