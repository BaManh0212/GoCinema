<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheLoai extends Model
{
    use HasFactory;

    protected $table = 'the_loai';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->belongsToMany(Phim::class, 'phim_the_loai', 'the_loai_id', 'phim_id');
    }
}
