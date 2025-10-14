<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboChiTiet extends Model
{
    use HasFactory;

    public $timestamps = false; 
    protected $table = 'combo_chi_tiet';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'combo_id',
        'san_pham_id',
        'so_luong',
    ];

    // Quan hệ với combo
    public function combo()
    {
        return $this->belongsTo(Combo::class, 'combo_id');
    }

    // Quan hệ với sản phẩm
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}