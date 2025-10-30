<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DanhMucController extends Controller
{
    // 📋 Danh sách danh mục
   public function index(Request $request)
{
    $query = DanhMuc::withCount('phims');

    // 🔍 Tìm kiếm theo tên
    if ($request->filled('q')) {
        $query->where('ten', 'like', '%' . $request->q . '%');
    }

    // 🔽 Sắp xếp
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

    $danhmucs = $query->paginate(10)->appends($request->query());

    return view('staff.danhmuc.index', [
        'danhmucs' => $danhmucs,
        'filters' => [
            'q' => $request->q,
            'sort' => $request->sort,
        ],
    ]);
}
    public function show($id)
    {
        // Lấy danh mục cùng các phim liên quan
        $danhmuc = DanhMuc::with('phims')->findOrFail($id);

        return view('staff.danhmuc.show', compact('danhmuc'));
    }   
}
