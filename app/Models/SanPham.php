<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;

    protected $table = 'san_pham';

    protected $fillable = [
        'ten',
        'gia',
        'mo_ta',
        'anh',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function combo()
    {
        return $this->belongsToMany(Combo::class, 'combo_chi_tiet', 'san_pham_id', 'combo_id')
                    ->withPivot('so_luong');
    }

    public function donHangSanPham()
    {
        return $this->hasMany(DonHangSanPham::class, 'san_pham_id');
    }
}
