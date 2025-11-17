<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GheSuatChieu extends Model
{
    use HasFactory;

    protected $table = 'ghe_suat_chieu';

    protected $fillable = [
        'suat_chieu_id',
        'ghe_id',
        'trang_thai',
    ];

public function ghe()
{
    return $this->belongsTo(Ghe::class, 'ghe_id', 'id');
}

    public function suatChieu()
    {
        return $this->belongsTo(SuatChieu::class);
    }
public function gheDaDat()
{
    return $this->hasMany(GheSuatChieu::class, 'don_dat_ve_id', 'id')->with('ghe');
}
}
