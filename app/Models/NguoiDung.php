<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\VaiTro;

class NguoiDung extends Authenticatable
{
	use HasFactory, Notifiable;
  public $timestamps = false;
	protected $table = 'nguoi_dung';

	protected $fillable = [
		'ho_ten',
		'email',
		'mat_khau',
		'so_dien_thoai',
		// allow mass assignment of 'password' so mutator setPasswordAttribute runs
		'password',
		'vai_tro_id',
		'kich_hoat',
		'loai_tai_khoan',
		'diem_tich_luy',
	];

	protected $hidden = [
		'mat_khau',
	];

	/**
	 * Return the password for authentication (maps to mat_khau column)
	 */
	public function getAuthPassword()
	{
		return $this->mat_khau;
	}

	/**
	 * When setting the password via $user->password = 'plain', store it into mat_khau hashed.
	 */
	public function setPasswordAttribute($value)
	{
		if ($value) {
			$this->attributes['mat_khau'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
		}
	}

	// Also allow setting mat_khau directly with hashing
	public function setMatKhauAttribute($value)
	{
		if ($value) {
			$this->attributes['mat_khau'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
		}
	}

	/**
	 * Relationship to VaiTro
	 */
	public function vaiTro(): BelongsTo
	{
		return $this->belongsTo(VaiTro::class, 'vai_tro_id');
	}

	/**
	 * Relationship to LichSuDiem
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
	 * Accessor để có thể dùng $user->diem
	 */
	public function getDiemAttribute()
	{
		return $this->diem_tich_luy ?? 0;
	}
}
