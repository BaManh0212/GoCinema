<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HanhDongNguoiDung extends Model
{
    use HasFactory;

    protected $table = 'hanh_dong_nguoi_dung';

    protected $fillable = [
        'nguoi_dung_id',
        'hanh_dong',
        'mo_ta',
    ];

    /**
     * Relationships
     */
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
