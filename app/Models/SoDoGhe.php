<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoDoGhe extends Model
{
    use HasFactory;

    protected $table = 'so_do_ghe';

    protected $fillable = [
        'phong_id',
        'ma_tran',
    ];

    protected $casts = [
        'ma_tran' => 'array', // tự động convert JSON <-> Array
    ];


     public function phong()
    {
        return $this->belongsTo(PhongChieu::class, 'phong_id');
        // 'phong_id' là FK trong bảng so_do_ghe
    }
public function ghe()
    {
        return $this->hasMany(Ghe::class, 'so_do_id');
    }
}
