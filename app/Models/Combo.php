<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'combo';

    protected $fillable = [
        'ten',
        'gia',
        'mo_ta',
        'so_luong',
    ];

    public $timestamps = true;

    // Quan hệ với combo_chi_tiet
    public function chiTiet()
    {
        return $this->hasMany(ComboChiTiet::class, 'combo_id');
    }
}
