<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCaoDoanhThuSuat extends Model
{
    use HasFactory;

    protected $table = 'bao_cao_doanh_thu_suat';

    protected $fillable = [
        'suat_chieu_id',
        'doanh_thu',
        'so_ve_ban',
    ];

    protected $casts = [
        'doanh_thu' => 'decimal:2',
        'so_ve_ban' => 'integer',
    ];

    /**
     * Relationships
     */
    public function suatChieu()
    {
        return $this->belongsTo(SuatChieu::class, 'suat_chieu_id');
    }
}
