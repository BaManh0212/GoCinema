<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Combo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'combo';

    protected $fillable = [
        'ten',
        'slug',
        'gia',
        'mo_ta',
        'so_luong',
    ];

    public $timestamps = true;

    // ===== Quan hệ với combo_chi_tiet =====
    public function chiTiet()
    {
        return $this->hasMany(ComboChiTiet::class, 'combo_id');
    }

    // ===== Tự động tạo slug khi tạo hoặc cập nhật =====
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($combo) {
            if (empty($combo->slug)) {
                $combo->slug = Str::slug($combo->ten);

                // Đảm bảo slug là duy nhất
                $originalSlug = $combo->slug;
                $count = 1;

                while (static::where('slug', $combo->slug)
                        ->where('id', '!=', $combo->id)
                        ->exists()) {
                    $combo->slug = "{$originalSlug}-{$count}";
                    $count++;
                }
            }
        });
    }
    public function donDatVeCombo()
{
    return $this->hasMany(DonDatVeCombo::class, 'combo_id');
}

}
