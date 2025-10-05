<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaoDien extends Model
{
    use HasFactory;

    protected $table = 'dao_dien';

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
        return $this->belongsToMany(Phim::class, 'phim_dao_dien', 'dao_dien_id', 'phim_id');
    }
}
