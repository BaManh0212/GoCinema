<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danh_gia';

    protected $fillable = [
        'phim_id',
        'nguoi_dung_id',
        'so_sao',
        'binh_luan',
    ];

    protected $casts = [
        'so_sao' => 'integer',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->belongsTo(Phim::class, 'phim_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
