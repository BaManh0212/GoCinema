<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'phim';

    protected $fillable = [
        'tieu_de',
        'mo_ta',
        'anh_poster',
        'trailer',
        'phu_de',
        'thoi_luong',
        'ngay_cong_chieu',
        'do_tuoi_gioi_han',
        'danh_muc_id',
        'ngon_ngu_id',
    ];

    protected $casts = [
        'phu_de' => 'boolean',
        'thoi_luong' => 'integer',
        'do_tuoi_gioi_han' => 'integer',
        'ngay_cong_chieu' => 'date',
    ];

    /**
     * Relationships
     */
    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'danh_muc_id');
    }

    public function ngonNgu()
    {
        return $this->belongsTo(NgonNgu::class, 'ngon_ngu_id');
    }

    public function theLoai()
    {
        return $this->belongsToMany(TheLoai::class, 'phim_the_loai', 'phim_id', 'the_loai_id');
    }

    public function dienVien()
    {
        return $this->belongsToMany(DienVien::class, 'phim_dien_vien', 'phim_id', 'dien_vien_id');
    }

    public function daoDien()
    {
        return $this->belongsToMany(DaoDien::class, 'phim_dao_dien', 'phim_id', 'dao_dien_id');
    }

    public function suatChieu()
    {
        return $this->hasMany(SuatChieu::class, 'phim_id');
    }

    public function danhGia()
    {
        return $this->hasMany(DanhGia::class, 'phim_id');
    }

    public function baoCaoDoanhThuPhim()
    {
        return $this->hasMany(BaoCaoDoanhThuPhim::class, 'phim_id');
    }

    /**
     * Helper methods
     */
    public function getDiemTrungBinhAttribute()
    {
        return $this->danhGia()->avg('so_sao');
    }

    public function getTongDanhGiaAttribute()
    {
        return $this->danhGia()->count();
    }
}
