<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BaiViet extends Model
{
    use HasFactory;

    protected $table = 'baiviets';

    protected $fillable = [
        'tieu_de', 'slug', 'hinh_anh', 'tom_tat', 'noi_dung', 
        'loai', 'is_active', 'ngay_phat_hanh', 'ngay_ket_thuc'
    ];

    // Tự động cast sang Carbon object
    protected $dates = [
        'ngay_phat_hanh',
        'ngay_ket_thuc',
        'created_at',
        'updated_at'
    ];

    // Kiểm tra bài viết có đang hiển thị hay không
    public function getIsCurrentlyActiveAttribute()
    {
        if (!$this->is_active || !$this->ngay_phat_hanh) {
            return false;
        }

        $today = Carbon::today();
        $start = Carbon::parse($this->ngay_phat_hanh);
        $end = $this->ngay_ket_thuc ? Carbon::parse($this->ngay_ket_thuc) : null;

        if ($end) {
            return $today->between($start, $end);
        }

        return $today->gte($start);
    }
}
