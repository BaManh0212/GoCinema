<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'voucher';

    protected $fillable = [
        'ten',
        'loai',
        'gia_tri',
        'gia_tri_don_hang_toi_thieu',
        'ap_dung_cho',
        'so_lan_su_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
    ];

    protected $casts = [
        'gia_tri' => 'decimal:2',
        'gia_tri_don_hang_toi_thieu' => 'decimal:2',
        'so_lan_su_dung' => 'integer',
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
    ];

    /**
     * Relationships
     */
    public function voucherNguoiDung()
    {
        return $this->hasMany(VoucherNguoiDung::class, 'voucher_id');
    }
}
