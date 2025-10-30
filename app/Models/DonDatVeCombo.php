<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonDatVeCombo extends Model
{
    use HasFactory;

    protected $table = 'don_dat_ve_combo';
    protected $fillable = [
        'don_dat_ve_id',
        'combo_id',
        'so_luong',
        'gia',
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'combo_id');
    }

    public function donDatVe()
    {
        return $this->belongsTo(DonDatVe::class, 'don_dat_ve_id');
    }
}
