<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'phim';

    protected $fillable = [
        'tieu_de',
        'mo_ta',
        'anh_poster',
        'trailer',
        'phu_de',
        'thoi_luong',
        'ngay_cong_chieu',
        'do_tuoi_gioi_han',
        'danh_muc_id',
        'ngon_ngu_id',
    ];

    public function suatChieus(): HasMany
    {
        return $this->hasMany(SuatChieu::class, 'phim_id');
    }

    public function theLoais(): BelongsToMany
    {
        return $this->belongsToMany(TheLoai::class, 'phim_the_loai', 'phim_id', 'the_loai_id');
    }
}
