<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Models\Phim;
use App\Models\NguoiDung;

class DanhGia extends Model
{
    protected $table = 'danh_gia';
    protected $fillable = ['phim_id', 'nguoi_dung_id', 'so_sao', 'binh_luan'];

    public function phim()
    {
        return $this->belongsTo(Phim::class, 'phim_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
