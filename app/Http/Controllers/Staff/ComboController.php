<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\ComboChiTiet;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    /** =============================
     * 🧩 DANH SÁCH COMBO
     * ============================== */
    public function index(Request $request)
    {
        $query = Combo::query();

        // Tìm theo tên combo
        if ($request->filled('q')) {
            $query->where('ten', 'like', '%' . $request->q . '%');
        }

        // Sắp xếp
        $sortType = $request->sort ?? 'moi_nhat'; // mặc định mới nhất
        switch ($sortType) {
            case 'gia_asc':
                $query->orderBy('gia', 'asc');
                break;
            case 'gia_desc':
                $query->orderBy('gia', 'desc');
                break;
            case 'cu_nhat':
                $query->orderBy('created_at', 'asc');
                break;
            case 'moi_nhat':
                $query->orderBy('created_at', 'desc');
                break;
        }

        $combos = $query->get();
        $filters = $request->only(['q', 'sort']);

        return view('staff.combo.index', compact('combos', 'filters'));
    }
}
