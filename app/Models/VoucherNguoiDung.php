<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherNguoiDung extends Model
{
    use HasFactory;

    protected $table = 'voucher_nguoi_dung';

    protected $fillable = [
        'nguoi_dung_id',
        'voucher_id',
        'diem_da_doi',
        'so_lan_da_dung',
        'ngay_doi',
        'ngay_han',
        'trang_thai'
    ];

    protected $casts = [
        'diem_da_doi' => 'integer',
        'so_lan_da_dung' => 'integer',
        'ngay_doi' => 'datetime',
        'ngay_han' => 'datetime'
    ];

    public $timestamps = true;

    // Quan hệ với người dùng
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    // Quan hệ với voucher
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    // Kiểm tra voucher còn sử dụng được không
    public function conSuDungDuoc()
    {
        if ($this->trang_thai !== 'chua_su_dung') {
            return false;
        }

        if ($this->ngay_han && $this->ngay_han->lt(now())) {
            return false;
        }

        if ($this->so_lan_da_dung >= $this->voucher->so_lan_su_dung) {
            return false;
        }

        return true;
    }

    // Scope lấy voucher chưa sử dụng
    public function scopeChuaSuDung($query)
    {
        return $query->where('trang_thai', 'chua_su_dung');
    }

    // Scope lấy voucher đã sử dụng
    public function scopeDaSuDung($query)
    {
        return $query->where('trang_thai', 'da_su_dung');
    }

    // Scope lấy voucher đã hết hạn
    public function scopeDaHetHan($query)
    {
        return $query->where('trang_thai', 'da_het_han');
    }
}
