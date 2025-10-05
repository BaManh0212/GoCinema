<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rap extends Model
{
    use HasFactory;

    protected $table = 'rap';

    protected $fillable = [
        'ten',
        'dia_chi',
    ];

    /**
     * Relationships
     */
    public function phongChieu()
    {
        return $this->hasMany(PhongChieu::class, 'rap_id');
    }

    public function baoCaoDoanhThuRap()
    {
        return $this->hasMany(BaoCaoDoanhThuRap::class, 'rap_id');
    }
}
