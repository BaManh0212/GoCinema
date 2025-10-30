<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;
use App\Models\ComboChiTiet;

class SanPhamController extends Controller
{
    /**
     * 🧾 Hiển thị danh sách sản phẩm
     */
    public function index(Request $request)
    {
        // Lấy query builder để thêm điều kiện lọc
        $query = SanPham::query();

        // Lọc theo từ khóa tên
        if ($request->filled('q')) {
            $query->where('ten', 'like', '%' . $request->q . '%');
        }

        // Sắp xếp
        $sortType = $request->sort ?? 'gia_desc'; // mặc định giá giảm dần
        switch ($sortType) {
            case 'gia_asc':
                $query->orderBy('gia', 'asc');
                break;
            case 'gia_desc':
                $query->orderBy('gia', 'desc');
                break;
        }

        $sanPhams = $query->get();
        $filters = $request->only(['q', 'sort']);

        return view('staff.san_pham.index', compact('sanPhams', 'filters'));
    }
}
