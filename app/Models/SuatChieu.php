<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuatChieu extends Model
{
    use HasFactory;

    protected $table = 'suat_chieu';

    protected $fillable = [
        'phim_id',
        'phong_id',
        'gio_bat_dau',
        'gio_ket_thuc',
        'gia_ve',
        'trang_thai', // ✅ thêm vào để có thể gán giá trị khi create/update
        'ly_do_huy',
    ];

    protected $casts = [
        'gio_bat_dau' => 'datetime',
        'gio_ket_thuc' => 'datetime',
    ];

    public $timestamps = false; // bạn đang dùng cột thời gian riêng, nên không cần timestamps mặc định

    /*
    |--------------------------------------------------------------------------
    | 🔗 Quan hệ
    |--------------------------------------------------------------------------
    */
public function phim()
{
    return $this->belongsTo(Phim::class, 'phim_id', 'id');
}

public function phong()
{
    return $this->belongsTo(PhongChieu::class, 'phong_id', 'id');
}
    // Giữ alias để tương thích nếu có view/controller cũ dùng
    public function phongChieu()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
    }
    // Quan hệ với ChiTietVe
    public function chiTietVe()
    {
        return $this->hasMany(ChiTietVe::class, 'suat_chieu_id', 'id');
    }


    /*
    |--------------------------------------------------------------------------
    | 🧠 Các scope / helper
    |--------------------------------------------------------------------------
    */

    // Lọc chỉ những suất đang hoạt động
    public function scopeHoatDong($query)
    {
        return $query->where('trang_thai', 'hoat_dong');
    }

    // Lọc các suất bị tạm dừng
    public function scopeTamDung($query)
    {
        return $query->where('trang_thai', 'tam_dung');
    }

    // Lọc các suất bị hủy
    public function scopeHuy($query)
    {
        return $query->where('trang_thai', 'huy');
    }

    // Kiểm tra trạng thái hiện tại (hữu ích trong view)
    public function isHoatDong()
    {
        return $this->trang_thai === 'hoat_dong';
    }

    public function isTamDung()
    {
        return $this->trang_thai === 'tam_dung';
    }

    public function isHuy()
    {
        return $this->trang_thai === 'huy';
    }
}
