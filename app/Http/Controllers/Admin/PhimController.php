<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phim;
use App\Models\DanhMuc;
use App\Models\NgonNgu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhimController extends Controller
{
    public function index()
    {
        $phims = Phim::with(['danhMuc', 'ngonNgu'])->withTrashed()->paginate(10);
        return view('admin.phim.index', compact('phims'));
    }

    public function create()
{
    $danhMucs = DanhMuc::all();
    $ngonNgus = NgonNgu::all();
    return view('admin.phim.create', compact('danhMucs', 'ngonNgus'));
}


    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'thoi_luong' => 'required|numeric|min:1',
            'danh_muc_id' => 'required|exists:danh_muc,id',
            'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
            'trailer' => 'nullable|string',
            'phu_de' => 'boolean',
            'ngay_cong_chieu' => 'nullable|date',
            'do_tuoi_gioi_han' => 'nullable|integer',
            'anh_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $posterPath = $request->hasFile('anh_poster')
            ? $request->file('anh_poster')->store('posters', 'public')
            : null;

        Phim::create([
            'tieu_de' => $request->tieu_de,
            'mo_ta' => $request->mo_ta,
            'thoi_luong' => $request->thoi_luong,
            'danh_muc_id' => $request->danh_muc_id,
            'ngon_ngu_id' => $request->ngon_ngu_id,
            'anh_poster' => $posterPath,
            'trailer' => $request->trailer,
            'phu_de' => $request->phu_de ?? false,
            'ngay_cong_chieu' => $request->ngay_cong_chieu,
            'do_tuoi_gioi_han' => $request->do_tuoi_gioi_han,
        ]);

        return redirect()->route('admin.phim.index')->with('success', 'Thêm phim thành công!');
    }

    public function edit($id)
    {
        $phim = Phim::withTrashed()->findOrFail($id);
        $danhMucs = DanhMuc::all();
        $ngonNgus = NgonNgu::all();
        return view('admin.phim.edit', compact('phim', 'danhMucs', 'ngonNgus'));
    }

    public function update(Request $request, $id)
    {
        $phim = Phim::withTrashed()->findOrFail($id);

        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'thoi_luong' => 'required|numeric|min:1',
            'danh_muc_id' => 'required|exists:danh_muc,id',
            'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
            'anh_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('anh_poster')) {
            if ($phim->anh_poster && Storage::disk('public')->exists($phim->anh_poster)) {
                Storage::disk('public')->delete($phim->anh_poster);
            }
            $phim->anh_poster = $request->file('anh_poster')->store('posters', 'public');
        }

        $phim->update($request->except('anh_poster'));

        return redirect()->route('admin.phim.index')->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy($id)
    {
        $phim = Phim::findOrFail($id);
        $phim->delete(); // Xóa mềm
        return redirect()->route('admin.phim.index')->with('success', 'Đã xóa phim!');
    }

    public function restore($id)
    {
        $phim = Phim::withTrashed()->findOrFail($id);
        $phim->restore();
        return redirect()->route('admin.phim.index')->with('success', 'Khôi phục phim thành công!');
    }
}
