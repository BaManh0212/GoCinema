<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgonNgu extends Model
{
    use HasFactory;

    protected $table = 'ngon_ngu';

    protected $fillable = ['ten_ngon_ngu'];

    public $timestamps = false;

    // Quan hệ: 1 Ngôn ngữ có nhiều Phim
    public function phims()
    {
        return $this->hasMany(Phim::class, 'ngon_ngu_id');
    }
}
