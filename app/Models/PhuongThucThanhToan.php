<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhuongThucThanhToan extends Model
{
    use HasFactory;

    protected $table = 'phuong_thuc_thanh_toan';

    protected $fillable = [
        'ten',
        'mo_ta',
        'kich_hoat',
    ];

    protected $casts = [
        'kich_hoat' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function thanhToan()
    {
        return $this->hasMany(ThanhToan::class, 'phuong_thuc_id');
    }

    /**
     * Scopes
     */
    public function scopeKichHoat($query)
    {
        return $query->where('kich_hoat', true);
    }
}
