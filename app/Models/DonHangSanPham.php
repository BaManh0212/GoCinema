<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHangSanPham extends Model
{
    use HasFactory;

    protected $table = 'don_hang_san_pham';

    protected $fillable = [
        'san_pham_id',
        'so_luong',
        'gia',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'gia' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}
