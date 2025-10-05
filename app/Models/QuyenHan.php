<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenHan extends Model
{
    use HasFactory;

    protected $table = 'quyen_han';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    /**
     * Relationships
     */
    public function vaiTro()
    {
        return $this->belongsToMany(VaiTro::class, 'vai_tro_quyen_han', 'quyen_han_id', 'vai_tro_id');
    }
}
