<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';

    protected $fillable = [
        'title',
        'genre',
        'release_date',
        'description',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
