<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanhMuc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'danh_muc';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    protected $dates = ['deleted_at'];

    public function phims()
    {
        return $this->belongsToMany(Phim::class, 'phim_danh_muc', 'danh_muc_id', 'phim_id');
    }
}
