<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $table = 'showtimes';

    protected $fillable = [
        'movie_id',
        'start_time',
        'price',
        'room',
    ];

    // Convert cột start_time thành kiểu datetime (Carbon)
    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
