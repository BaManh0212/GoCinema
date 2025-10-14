<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';
    protected $fillable = ['ten'];

    // Quan hệ 1-nhiều với Phim
    public function phims()
    {
        return $this->hasMany(Phim::class, 'danh_muc_id');
    }
}
