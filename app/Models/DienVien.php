<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DienVien extends Model
{
    use HasFactory;

    protected $table = 'dien_vien';

    protected $fillable = [
        'ten',
        'anh',
        'tieu_su',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->belongsToMany(Phim::class, 'phim_dien_vien', 'dien_vien_id', 'phim_id');
    }
}
