<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'voucher';

    protected $fillable = [
        'ten',
        'loai',
        'gia_tri',
        'giam_toi_da',
        'gia_tri_don_hang_toi_thieu',
        'ap_dung_cho',
        'so_lan_su_dung',
        'so_luong_toi_da',
        'so_luong_da_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'diem_can',
        'kich_hoat'
    ];

    protected $casts = [
        'gia_tri' => 'decimal:2',
        'giam_toi_da' => 'decimal:2',
        'gia_tri_don_hang_toi_thieu' => 'decimal:2',
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'diem_can' => 'integer',
        'so_lan_su_dung' => 'integer',
        'so_luong_toi_da' => 'integer',
        'so_luong_da_dung' => 'integer',
        'kich_hoat' => 'boolean'
    ];

    public $timestamps = true;

    // Quan hệ với người dùng đã đổi voucher
    public function nguoiDungDaDoi()
    {
        return $this->belongsToMany(NguoiDung::class, 'voucher_nguoi_dung')
                    ->withPivot('diem_da_doi', 'so_lan_da_dung', 'ngay_doi', 'ngay_han', 'trang_thai')
                    ->withTimestamps();
    }

    // Kiểm tra voucher có còn hiệu lực không
    public function conHieuLuc()
    {
        $now = now()->startOfDay();
        
        if ($this->ngay_bat_dau && $this->ngay_bat_dau->gt($now)) {
            return false;
        }
        
        if ($this->ngay_ket_thuc && $this->ngay_ket_thuc->lt($now)) {
            return false;
        }
        
        return true;
    }

    // Lấy mô tả giá trị voucher
    public function getMoTaGiaTriAttribute()
    {
        if ($this->loai === 'phan_tram') {
            $moTa = number_format($this->gia_tri, 0) . '%';
            
            // Nếu có giới hạn giảm tối đa (như Shopee)
            if ($this->giam_toi_da > 0) {
                $moTa .= ' (tối đa ' . number_format($this->giam_toi_da, 0) . 'đ)';
            }
            
            return $moTa;
        }
        return number_format($this->gia_tri, 0) . 'đ';
    }

    // Lấy mô tả áp dụng
    public function getMoTaApDungAttribute()
    {
        return match($this->ap_dung_cho) {
            've' => 'Vé xem phim',
            'san_pham' => 'Sản phẩm',
            'tat_ca' => 'Tất cả',
            default => 'Tất cả'
        };
    }

    // Scope chỉ lấy voucher đang kích hoạt
    public function scopeKichHoat($query)
    {
        return $query->where('kich_hoat', true);
    }

    // Scope lấy voucher còn hiệu lực
    public function scopeConHieuLuc($query)
    {
        $now = now()->startOfDay();
        return $query->where(function($q) use ($now) {
            $q->whereNull('ngay_bat_dau')->orWhere('ngay_bat_dau', '<=', $now);
        })->where(function($q) use ($now) {
            $q->whereNull('ngay_ket_thuc')->orWhere('ngay_ket_thuc', '>=', $now);
        });
    }

    // Kiểm tra còn voucher để đổi không
    public function conVoucherDeDoi()
    {
        return $this->so_luong_da_dung < $this->so_luong_toi_da;
    }

    // Lấy số lượng còn lại
    public function getSoLuongConLaiAttribute()
    {
        return max(0, $this->so_luong_toi_da - $this->so_luong_da_dung);
    }
}
