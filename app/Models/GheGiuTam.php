<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public $timestamps = true;

    public function suatChieu(): BelongsTo
    {
        return $this->belongsTo(SuatChieu::class, 'suat_chieu_id');
    }

    public function ghe(): BelongsTo
    {
        return $this->belongsTo(Ghe::class, 'ghe_id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
