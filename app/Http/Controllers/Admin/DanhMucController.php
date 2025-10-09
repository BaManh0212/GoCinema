<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    public function index()
    {
        $danhmucs = DanhMuc::all();
        return view('admin.danhmuc.index', compact('danhmucs'));
    }

    public function create()
    {
        return view('admin.danhmuc.create');
    }

    public function store(Request $request)
    {
        $request->validate(['ten' => 'required']);
        DanhMuc::create($request->all());
        return redirect()->route('admin.danhmuc.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $danhmuc = DanhMuc::findOrFail($id);
        return view('admin.danhmuc.edit', compact('danhmuc'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['ten' => 'required']);
        $danhmuc = DanhMuc::findOrFail($id);
        $danhmuc->update($request->all());
        return redirect()->route('admin.danhmuc.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        DanhMuc::destroy($id);
        return redirect()->route('admin.danhmuc.index')->with('success', 'Xóa thành công!');
    }
}
