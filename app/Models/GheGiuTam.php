<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GheGiuTam extends Model
{
    use HasFactory;

    protected $table = 'ghe_giu_tam';

    protected $fillable = [
        'suat_chieu_id',
        'ghe_id',
        'nguoi_dung_id',
        'het_han',
    ];

    protected $casts = [
        'het_han' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function suatChieu()
    {
        return $this->belongsTo(SuatChieu::class, 'suat_chieu_id');
    }

    public function ghe()
    {
        return $this->belongsTo(Ghe::class, 'ghe_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Scopes
     */
    public function scopeConHan($query)
    {
        return $query->where('het_han', '>', now());
    }

    public function scopeHetHan($query)
    {
        return $query->where('het_han', '<=', now());
    }
}
