<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ghe extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ghe';

    protected $fillable = [
        'phong_id',
        'hang',
        'cot',
        'loai',
        'trang_thai',
    ];

    protected $casts = [
        'cot' => 'integer',
    ];

    /**
     * Relationships
     */
    public function phongChieu()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }

    public function chiTietVe()
    {
        return $this->hasMany(ChiTietVe::class, 'ghe_id');
    }

    public function gheGiuTam()
    {
        return $this->hasMany(GheGiuTam::class, 'ghe_id');
    }

    /**
     * Scopes
     */
    public function scopeHoatDong($query)
    {
        return $query->where('trang_thai', 'hoat_dong');
    }

    public function scopeVip($query)
    {
        return $query->where('loai', 'vip');
    }

    public function scopeThuong($query)
    {
        return $query->where('loai', 'thuong');
    }

    public function scopeDoi($query)
    {
        return $query->where('loai', 'doi');
    }

    /**
     * Helper methods
     */
    public function getTenGheAttribute()
    {
        return $this->hang . $this->cot;
    }
}
