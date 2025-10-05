<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rap extends Model
{
    use HasFactory;

    protected $table = 'rap';

    protected $fillable = [
        'ten',
        'dia_chi',
    ];

    public function phongChieus(): HasMany
    {
        return $this->hasMany(PhongChieu::class, 'rap_id');
    }
}
