<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ghe extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ghe';

    protected $fillable = [
        'phong_id',
        'hang',
        'cot',
        'loai',
        'trang_thai',
    ];

    public function phong(): BelongsTo
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }

    public function chiTietVes(): HasMany
    {
        return $this->hasMany(ChiTietVe::class, 'ghe_id');
    }
}
