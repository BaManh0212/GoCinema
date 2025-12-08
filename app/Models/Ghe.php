<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ghe extends Model
{
    use HasFactory;

    protected $table = 'ghe';

    protected $fillable = [
        'phong_id', 'hang', 'cot', 'loai', 'trang_thai'
    ];

    /**
     * Relationship: Ghế thuộc về một phòng chiếu
     */
    public function phongChieu()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }
}
