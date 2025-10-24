<?php

namespace App\Http\Controllers\Admin;

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

    return view('admin.danhmuc.index', [
        'danhmucs' => $danhmucs,
        'filters' => [
            'q' => $request->q,
            'sort' => $request->sort,
        ],
    ]);
}



    // ➕ Form thêm danh mục
    public function create()
    {
        return view('admin.danhmuc.create');
    }

    // 💾 Xử lý thêm danh mục
    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:danh_muc,ten',
        ], [
            'ten.required' => 'Tên danh mục không được để trống.',
            'ten.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        DanhMuc::create([
            'ten' => $request->ten,
            'slug' => Str::slug($request->ten),
        ]);

        return redirect()->route('admin.danhmuc.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    // ✏️ Form sửa danh mục
    public function edit($id)
    {
        $danhmuc = DanhMuc::findOrFail($id);
        return view('admin.danhmuc.edit', compact('danhmuc'));
    }

    // 🔄 Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $danhmuc = DanhMuc::findOrFail($id);

        $request->validate([
            'ten' => 'required|string|max:255|unique:danh_muc,ten,' . $id,
        ], [
            'ten.required' => 'Tên danh mục không được để trống.',
            'ten.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        $danhmuc->update([
            'ten' => $request->ten,
            'slug' => Str::slug($request->ten),
        ]);

        return redirect()->route('admin.danhmuc.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }
    public function show($id)
    {
        // Lấy danh mục cùng các phim liên quan
        $danhmuc = DanhMuc::with('phims')->findOrFail($id);

        return view('admin.danhmuc.show', compact('danhmuc'));
    }   


    // 🗑️ Xóa mềm danh mục
    public function destroy($id)
    {
        $danhmuc = DanhMuc::findOrFail($id);

        if ($danhmuc->phims()->count() > 0) {
            return redirect()->route('admin.danhmuc.index')
                ->with('error', 'Không thể xóa danh mục vì vẫn còn phim bên trong!');
        }

        $danhmuc->delete();

        return redirect()->route('admin.danhmuc.index')
            ->with('success', 'Đã chuyển danh mục vào thùng rác!');
    }

    // 🗃️ Danh sách danh mục đã xóa
    public function trashed(Request $request)
    {
        $query = DanhMuc::onlyTrashed();

        // 🔍 Tìm kiếm theo tên
        if ($request->filled('keyword')) {
            $query->where('ten', 'like', '%' . $request->keyword . '%');
        }

        // 🔽 Sắp xếp theo ngày xóa mới nhất
        $danhmucs = $query->orderByDesc('deleted_at')
                          ->paginate(10)
                          ->appends($request->query());

        return view('admin.danhmuc.trashed', compact('danhmucs'));
}


    // 🔁 Khôi phục danh mục
    public function restore($id)
    {
        $danhmuc = DanhMuc::onlyTrashed()->findOrFail($id);
        $danhmuc->restore();

        return redirect()->route('admin.danhmuc.trashed')
            ->with('success', 'Khôi phục danh mục thành công!');
    }

    // ❌ Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $danhmuc = DanhMuc::onlyTrashed()->findOrFail($id);
        $danhmuc->forceDelete();

        return redirect()->route('admin.danhmuc.trashed')
            ->with('success', 'Xóa vĩnh viễn danh mục thành công!');
    }
}
