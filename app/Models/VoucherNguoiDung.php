<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherNguoiDung extends Model
{
    use HasFactory;

    protected $table = 'voucher_nguoi_dung';

    protected $fillable = [
        'voucher_id',
        'nguoi_dung_id',
        'da_su_dung',
        'ngay_su_dung',
    ];

    protected $casts = [
        'da_su_dung' => 'boolean',
        'ngay_su_dung' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
