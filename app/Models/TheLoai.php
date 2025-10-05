<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TheLoai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'the_loai';

    protected $fillable = [
        'ten',
        'mo_ta',
    ];

    public function phims(): BelongsToMany
    {
        return $this->belongsToMany(Phim::class, 'phim_the_loai', 'the_loai_id', 'phim_id');
    }
}
