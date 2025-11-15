<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Banner extends Model
{
    protected $fillable = [
    'title',
    'type',
    'image',
    'video_url',
    'link',
    'is_active',
    'display_order',
    'start_at',
    'end_at',
];


    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where(function ($q) use ($now) {
                         $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
                     })
                     ->where(function ($q) use ($now) {
                         $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
                     })
                     ->orderBy('display_order');
    }
}
