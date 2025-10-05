<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'combo';

    protected $fillable = [
        'ten',
        'gia',
        'mo_ta',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function sanPham()
    {
        return $this->belongsToMany(SanPham::class, 'combo_chi_tiet', 'combo_id', 'san_pham_id')
                    ->withPivot('so_luong');
    }

    public function donDatVeCombo()
    {
        return $this->hasMany(DonDatVeCombo::class, 'combo_id');
    }
}
