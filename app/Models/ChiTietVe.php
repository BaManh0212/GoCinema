<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietVe extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_ve';

    protected $fillable = [
        'don_dat_ve_id',
        'ghe_id',
        'gia',
        'loai_ghe',
        'trang_thai',
        'thoi_gian_su_dung',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'thoi_gian_su_dung' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function donDatVe()
    {
        return $this->belongsTo(DonDatVe::class, 'don_dat_ve_id');
    }

    public function ghe()
    {
        return $this->belongsTo(Ghe::class, 'ghe_id');
    }
}
