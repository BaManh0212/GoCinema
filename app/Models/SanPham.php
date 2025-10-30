<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SanPham extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'san_pham';

    protected $fillable = [
        'ten',
        'slug',
        'gia',
        'so_luong',
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sanPham) {
            $sanPham->slug = Str::slug($sanPham->ten);
        });

        static::updating(function ($sanPham) {
            $sanPham->slug = Str::slug($sanPham->ten);
        });
    }
}
