<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Tên bảng trong database
     */
    protected $table = 'nguoi_dung';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'vai_tro_id',
        'kich_hoat',
        'loai_tai_khoan',
        'diem',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    /**
     * Get the password attribute name
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mat_khau' => 'hashed',
            'kich_hoat' => 'boolean',
            'diem' => 'integer',
        ];
    }

    /**
     * Relationships
     */
    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }

    public function donDatVe()
    {
        return $this->hasMany(DonDatVe::class, 'nguoi_dung_id');
    }

    public function danhGia()
    {
        return $this->hasMany(DanhGia::class, 'nguoi_dung_id');
    }

    public function lichSuDiem()
    {
        return $this->hasMany(LichSuDiem::class, 'nguoi_dung_id');
    }

    public function hanhDongNguoiDung()
    {
        return $this->hasMany(HanhDongNguoiDung::class, 'nguoi_dung_id');
    }

    public function gheGiuTam()
    {
        return $this->hasMany(GheGiuTam::class, 'nguoi_dung_id');
    }

    public function voucherNguoiDung()
    {
        return $this->hasMany(VoucherNguoiDung::class, 'nguoi_dung_id');
    }

    /**
     * Scopes
     */
    public function scopeKichHoat($query)
    {
        return $query->where('kich_hoat', true);
    }

    public function scopeKhachHang($query)
    {
        return $query->where('loai_tai_khoan', 'khach_hang');
    }

    public function scopeNhanVien($query)
    {
        return $query->where('loai_tai_khoan', 'nhan_vien');
    }

    public function scopeQuanLy($query)
    {
        return $query->where('loai_tai_khoan', 'quan_ly');
    }
}
