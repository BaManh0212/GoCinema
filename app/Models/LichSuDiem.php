<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuDiem extends Model
{
    use HasFactory;

    protected $table = 'lich_su_diem';

    protected $fillable = [
        'nguoi_dung_id',
        'diem',
        'hanh_dong',
        'mo_ta',
    ];

    protected $casts = [
        'diem' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    public function scopeTichLuy($query)
    {
        return $query->where('hanh_dong', 'tich_luy');
    }

    public function scopeSuDung($query)
    {
        return $query->where('hanh_dong', 'su_dung');
    }
}
