<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCaoDoanhThuPhim extends Model
{
    use HasFactory;

    protected $table = 'bao_cao_doanh_thu_phim';

    protected $fillable = [
        'phim_id',
        'ngay',
        'doanh_thu',
        'so_ve_ban',
    ];

    protected $casts = [
        'ngay' => 'date',
        'doanh_thu' => 'decimal:2',
        'so_ve_ban' => 'integer',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->belongsTo(Phim::class, 'phim_id');
    }
}
