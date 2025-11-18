<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaGiamGia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ma_giam_gia';
    
    protected static function boot()
    {
        parent::boot();

        // Listen for all queries on the ma_giam_gia table
        DB::listen(function ($query) {
            if (str_contains($query->sql, 'ma_giam_gia')) {
                \Log::info('MaGiamGia Query:', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });
    }

    protected $fillable = [
        'ma', 'loai', 'gia_tri', 'giam_toi_da',
        'gia_tri_don_hang_toi_thieu', 'ap_dung_cho',
        'so_luong', 'so_lan_su_dung', 'kich_hoat',
        'ngay_bat_dau', 'ngay_ket_thuc'
    ];

    protected $casts = [
        'kich_hoat' => 'boolean',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
    ];

    /** Kiểm tra mã còn hiệu lực */
    public function getIsActiveAttribute(): bool
    {
        $now = Carbon::now();
        return $this->kich_hoat &&
               ($this->ngay_bat_dau === null || $this->ngay_bat_dau <= $now) &&
               ($this->ngay_ket_thuc === null || $this->ngay_ket_thuc >= $now) &&
               $this->so_luong > 0;
    }

    /** Định dạng giá trị giảm */
    public function getFormattedValueAttribute(): string
    {
        return $this->loai === 'phan_tram'
            ? $this->gia_tri . '%'
            : number_format($this->gia_tri, 0, ',', '.') . 'đ';
    }
}