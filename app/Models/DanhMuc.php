<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->hasMany(Phim::class, 'danh_muc_id');
    }
}
