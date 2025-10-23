<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    public function index(Request $request)
{
    $query = DanhMuc::withCount('phims');

    // 🔍 Tìm kiếm theo tên
    if ($request->filled('search')) {
        $query->where('ten', 'like', '%' . $request->search . '%');
    }

    // 🔽 Sắp xếp
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('ten', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('ten', 'desc');
                break;
            case 'phim_count_desc':
                $query->orderBy('phims_count', 'desc');
                break;
            case 'phim_count_asc':
                $query->orderBy('phims_count', 'asc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    $danhmucs = $query->paginate(10)->appends($request->query());

    return view('staff.danhmuc.index', compact('danhmucs'));
}
}
