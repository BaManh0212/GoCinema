<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phim extends Model
{
    use HasFactory;

    protected $table = 'phim';
    public $timestamps = false;
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
        'ngay_tao',
        'ngay_cap_nhat',
        'ngay_xoa'
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'danh_muc_id');
    }
    public function ngonNgu()
    {
        return $this->belongsTo(NgonNgu::class, 'ngon_ngu_id');
    }
    public function theLoais()
    {
        return $this->belongsToMany(TheLoai::class, 'phim_the_loai', 'phim_id', 'the_loai_id');
    }
    public function dinhDangs()
    {
        return $this->belongsToMany(DinhDang::class, 'phim_dinh_dang', 'phim_id', 'dinh_dang_id');
    }
}