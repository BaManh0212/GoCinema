<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function rap(): BelongsTo
    {
        return $this->belongsTo(Rap::class, 'rap_id');
    }

    public function ghes(): HasMany
    {
        return $this->hasMany(Ghe::class, 'phong_id');
    }

    public function suatChieus(): HasMany
    {
        return $this->hasMany(SuatChieu::class, 'phong_id');
    }
}
