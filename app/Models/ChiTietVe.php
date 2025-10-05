<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function donDatVe(): BelongsTo
    {
        return $this->belongsTo(DonDatVe::class, 'don_dat_ve_id');
    }

    public function ghe(): BelongsTo
    {
        return $this->belongsTo(Ghe::class, 'ghe_id');
    }
}
