<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rap extends Model
{
    use HasFactory;

    protected $table = 'rap'; // Tên bảng trong database

    protected $fillable = [
    'ten', 'dia_chi', 'so_dien_thoai', 'email', 'logo'
];

    public function phongchieus()
{
    return $this->hasMany(PhongChieu::class, 'rap_id');
}

}
