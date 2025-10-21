<?php

namespace App\Models;
use App\Models\Ghe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhongChieu extends Model
{
    use HasFactory;

    protected $table = 'phong_chieu'; // Tên bảng trong database

    protected $fillable = [
        'rap_id',
        'ten',
        'tong_ghe',
        'so_do',
        'dinh_dang_id',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
        'ngay_xoa',
    ];

    public $timestamps = false; // Vì bảng đang dùng cột ngày_tao / ngày_cap_nhat thay cho timestamps mặc định

    /**
     * Quan hệ: Phòng chiếu thuộc về một rạp.
     */
    public function rap()
    {
        return $this->belongsTo(Rap::class, 'rap_id');
    }

    /**
     * Quan hệ: Phòng chiếu có một định dạng chiếu (2D, 3D, IMAX, v.v...).
     */
    public function dinhDang()
    {
        return $this->belongsTo(DinhDang::class, 'dinh_dang_id');
    }
      // Quan hệ với Ghe (một phòng có nhiều ghế)
    public function ghe()
    {
        return $this->hasMany(Ghe::class, 'phong_id', 'id');
    }
    public function ghes()
    {
    return $this->hasMany(Ghe::class, 'phong_id', 'id');
    }
}
