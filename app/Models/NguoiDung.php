<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\VaiTro;
use App\Models\LichSuDiem;

class NguoiDung extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = true;
    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'password',
        'vai_tro_id',
        'kich_hoat',
        'loai_tai_khoan',
        'diem_tich_luy',
        'avatar', // ✅ thêm dòng này
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    /**
     * Trả về mật khẩu cho hệ thống Auth (ứng với cột mat_khau)
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /**
     * Khi set password (ảo) → lưu vào mat_khau đã hash
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['mat_khau'] = Hash::needsRehash($value)
                ? Hash::make($value)
                : $value;
        }
    }

    /**
     * Khi set mat_khau trực tiếp → tự hash
     */
    public function setMatKhauAttribute($value)
    {
        if ($value) {
            $this->attributes['mat_khau'] = Hash::needsRehash($value)
                ? Hash::make($value)
                : $value;
        }
    }

    /**
     * Quan hệ với VaiTro
     */
    public function vaiTro(): BelongsTo
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }

    /**
     * Quan hệ với lịch sử điểm
     */
    public function lichSuDiem()
    {
        return $this->hasMany(LichSuDiem::class, 'nguoi_dung_id');
    }

    /**
     * Thêm điểm cho người dùng
     */
    public function themDiem($diem, $moTa = '')
    {
        $this->diem_tich_luy += $diem;
        $this->save();

        LichSuDiem::create([
            'nguoi_dung_id' => $this->id,
            'diem' => $diem,
            'hanh_dong' => 'tich_luy',
            'mo_ta' => $moTa,
        ]);

        return $this;
    }

    /**
     * Trừ điểm của người dùng
     */
    public function truDiem($diem, $moTa = '')
    {
        if ($this->diem_tich_luy < $diem) {
            throw new \Exception('Không đủ điểm để thực hiện giao dịch này');
        }

        $this->diem_tich_luy -= $diem;
        $this->save();

        LichSuDiem::create([
            'nguoi_dung_id' => $this->id,
            'diem' => $diem,
            'hanh_dong' => 'su_dung',
            'mo_ta' => $moTa,
        ]);

        return $this;
    }

    /**
     * Accessor để lấy thuộc tính $user->diem
     */
    public function getDiemAttribute()
    {
        return $this->diem_tich_luy ?? 0;
    }

    /**
     * Accessor để lấy đường dẫn ảnh đầy đủ
     */
    public function getAvatarUrlAttribute()
{
    if ($this->avatar && file_exists(public_path('uploads/avatars/' . $this->avatar))) {
        return asset('uploads/avatars/' . $this->avatar);
    }

    // Ảnh mặc định
    return asset('uploads/avatars/default.png');
}

}
