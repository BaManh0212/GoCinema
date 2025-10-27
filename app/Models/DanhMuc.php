<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DanhMuc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'danh_muc';

    protected $fillable = [
        'ten',
        'slug',
    ];

    protected $dates = ['deleted_at'];

    // 🔹 Tự động tạo slug từ tên danh mục khi tạo hoặc cập nhật
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($danhMuc) {
            if (empty($danhMuc->slug)) {
                $danhMuc->slug = Str::slug($danhMuc->ten);
            }
        });

        static::updating(function ($danhMuc) {
            if (empty($danhMuc->slug)) {
                $danhMuc->slug = Str::slug($danhMuc->ten);
            }
        });
    }

    public function phims()
    {
        return $this->belongsToMany(Phim::class, 'phim_danh_muc', 'danh_muc_id', 'phim_id');
    }
}
