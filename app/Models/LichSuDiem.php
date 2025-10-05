<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuDiem extends Model
{
    use HasFactory;

    protected $table = 'lich_su_diem';

    protected $fillable = [
        'nguoi_dung_id',
        'diem_thay_doi',
        'ly_do',
        'loai',
    ];

    protected $casts = [
        'diem_thay_doi' => 'integer',
    ];

    /**
     * Relationships
     */
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
