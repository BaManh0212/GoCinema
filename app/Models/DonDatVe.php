<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonDatVe extends Model
{
    use HasFactory;

    protected $table = 'don_dat_ve';

    protected $fillable = [
        'ma_don',
        'nguoi_dung_id',
        'suat_chieu_id',
        'ma_giam_gia_id',
        'tong_tien',
        'trang_thai',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

public function suatChieu()
{
    return $this->belongsTo(SuatChieu::class, 'suat_chieu_id', 'id');
}

    public function maGiamGia()
    {
        return $this->belongsTo(MaGiamGia::class, 'ma_giam_gia_id');
    }

    public function chiTietVes()
    {
        return $this->hasMany(ChiTietVe::class, 'don_dat_ve_id');
    }
    public function combos()
{
    return $this->belongsToMany(
        Combo::class,
        'don_dat_ve_combo',
        'don_dat_ve_id',
        'combo_id'
    )->withPivot('so_luong', 'gia');
}
// Ghế đã đặt
public function gheDaDat()
{
    return $this->hasMany(GheSuatChieu::class, 'don_dat_ve_id', 'id')->with('ghe');
}

// Trả về mảng payload chuẩn cho QR
public function qrPayload()
{
    $this->loadMissing(['chiTietVes.ghe', 'suatChieu', 'combos']);

    $createdAt = $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');

    // Ghép danh sách ghế dạng "B6", "C10", ...
    $seats = $this->chiTietVes->map(function($ve) {
        $hang = $ve->ghe->hang ?? '';
        $cot  = $ve->ghe->cot ?? '';
        return trim($hang . $cot);
    })->filter()->values()->all();

    // Combos: chỉ gửi id + so_luong (tên có thể chứa Unicode)
    $combos = $this->combos->map(function($c) {
        return [
            'id' => (int) $c->id,
            'so_luong' => (int) ($c->pivot->so_luong ?? 0),
        ];
    })->values()->all();

    return [
        'ma_don' => (string) $this->ma_don,
        'ma_lay_ve' => strtoupper(substr(md5($this->id), 0, 7)),
        'don_dat_id' => (int) $this->id,
        'suat_chieu_id' => (int) $this->suat_chieu_id,
        'ngay_dat' => $createdAt,
        'seats' => $seats,
        'combos' => $combos,
        'tong_tien' => (float) $this->tong_tien,
        'trang_thai' => (string) ($this->trang_thai ?? ''),
    ];
}

// Trả về chuỗi JSON (không escape Unicode) để dùng làm nội dung QR
public function qrString()
{
    $payload = [
        'ma_don' => $this->ma_don,
        'ma_lay_ve' => strtoupper(substr(md5($this->id), 0, 7)),
    ];
    return json_encode($payload, JSON_UNESCAPED_UNICODE);
}
}
