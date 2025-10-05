<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgonNgu extends Model
{
    use HasFactory;

    protected $table = 'ngon_ngu';

    protected $fillable = [
        'ten',
        'ma',
    ];

    /**
     * Relationships
     */
    public function phim()
    {
        return $this->hasMany(Phim::class, 'ngon_ngu_id');
    }
}
