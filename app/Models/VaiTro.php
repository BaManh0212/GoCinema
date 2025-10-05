<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    protected $table = 'vai_tro';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    /**
     * Relationships
     */
    public function nguoiDung()
    {
        return $this->hasMany(User::class, 'vai_tro_id');
    }

    public function quyenHan()
    {
        return $this->belongsToMany(QuyenHan::class, 'vai_tro_quyen_han', 'vai_tro_id', 'quyen_han_id');
    }
}
