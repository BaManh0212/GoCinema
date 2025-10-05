<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCaoDoanhThuRap extends Model
{
    use HasFactory;

    protected $table = 'bao_cao_doanh_thu_rap';

    protected $fillable = [
        'rap_id',
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
    public function rap()
    {
        return $this->belongsTo(Rap::class, 'rap_id');
    }
}
