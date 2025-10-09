<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phim;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class PhimController extends Controller
{
    public function index()
    {
        $phims = Phim::with('danhMuc')->get();
        return view('admin.phim.index', compact('phims'));
    }

    public function create()
    {
        $danhmucs = DanhMuc::all();
        return view('admin.phim.create', compact('danhmucs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required',
            'danh_muc_id' => 'required'
        ]);

        $data = $request->all();
        if ($request->hasFile('anh_poster')) {
            $file = $request->file('anh_poster');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/poster'), $fileName);
            $data['anh_poster'] = 'uploads/poster/' . $fileName;
        }

        Phim::create($data);
        return redirect()->route('phim.index')->with('success', 'Thêm phim thành công!');
    }

    public function edit($id)
    {
        $phim = Phim::findOrFail($id);
        $danhmucs = DanhMuc::all();
        return view('admin.phim.edit', compact('phim', 'danhmucs'));
    }

    public function update(Request $request, $id)
    {
        $phim = Phim::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('anh_poster')) {
            $file = $request->file('anh_poster');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/poster'), $fileName);
            $data['anh_poster'] = 'uploads/poster/' . $fileName;
        }

        $phim->update($data);
        return redirect()->route('phim.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        Phim::destroy($id);
        return redirect()->route('phim.index')->with('success', 'Xóa phim thành công!');
    }
}
