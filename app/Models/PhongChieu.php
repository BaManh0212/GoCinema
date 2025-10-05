<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhongChieu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'phong_chieu';

    protected $fillable = [
        'rap_id',
        'ten',
        'tong_ghe',
        'so_do',
        'dinh_dang_id',
        'trang_thai',
    ];

    protected $casts = [
        'tong_ghe' => 'integer',
    ];

    /**
     * Relationships
     */
    public function rap()
    {
        return $this->belongsTo(Rap::class, 'rap_id');
    }

    public function dinhDang()
    {
        return $this->belongsTo(DinhDang::class, 'dinh_dang_id');
    }

    public function ghe()
    {
        return $this->hasMany(Ghe::class, 'phong_id');
    }

    public function suatChieu()
    {
        return $this->hasMany(SuatChieu::class, 'phong_id');
    }

    /**
     * Scopes
     */
    public function scopeHoatDong($query)
    {
        return $query->where('trang_thai', 'hoat_dong');
    }

    public function scopeBaoTri($query)
    {
        return $query->where('trang_thai', 'bao_tri');
    }
}
