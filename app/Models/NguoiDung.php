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

	protected $table = 'nguoi_dung';

	protected $fillable = [
		'ho_ten',
		'email',
		'mat_khau',
		// allow mass assignment of 'password' so mutator setPasswordAttribute runs
		'password',
		'vai_tro_id',
		'kich_hoat',
		'loai_tai_khoan',
		'diem',
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
}
