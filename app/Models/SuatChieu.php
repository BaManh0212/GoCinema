<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuatChieu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suat_chieu';

    protected $fillable = [
        'phim_id',
        'phong_id',
        'gio_bat_dau',
        'gio_ket_thuc',
        'gia_ve',
    ];

    public function phim(): BelongsTo
    {
        return $this->belongsTo(Phim::class, 'phim_id');
    }

    public function phong(): BelongsTo
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }

    public function donDatVes(): HasMany
    {
        return $this->hasMany(DonDatVe::class, 'suat_chieu_id');
    }
}
