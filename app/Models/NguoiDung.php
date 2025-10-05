<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NguoiDung extends Model
{
    use HasFactory;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'vai_tro_id',
        'kich_hoat',
        'loai_tai_khoan',
        'diem',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    public function donDatVes(): HasMany
    {
        return $this->hasMany(DonDatVe::class, 'nguoi_dung_id');
    }
}
