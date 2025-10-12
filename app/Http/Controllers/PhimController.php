<?php

namespace App\Http\Controllers;

use App\Models\Phim;
use App\Models\DanhMuc;
use App\Models\NgonNgu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TheLoai;
use App\Models\DinhDang;

class PhimController extends Controller
{
    // Hiển thị danh sách phim
    public function index()
{
    $phims = Phim::with(['danhMuc', 'ngonNgu'])->paginate(10);
    return view('admin.phim.index', compact('phims'));
}


    // Hiển thị form thêm phim mới
    public function create()
{
    $danhMucs = DanhMuc::all();
    $ngonNgus = NgonNgu::all();
    $theLoais = TheLoai::all();
    $dinhDangs = DinhDang::all(); // 👈 Thêm dòng này

    return view('admin.phim.create', compact('danhMucs', 'ngonNgus', 'theLoais', 'dinhDangs'));
}



    // Lưu phim mới vào DB
    public function store(Request $request)
{
    $request->validate([
        'tieu_de' => 'required|string|max:255',
        'mo_ta' => 'nullable|string',
        'thoi_luong' => 'required|numeric|min:1',
        'danh_muc_id' => 'required|exists:danh_muc,id',
        'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
        'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $posterPath = null;
    if ($request->hasFile('poster')) {
        $posterPath = $request->file('poster')->store('posters', 'public');
    }

    Phim::create([
        'tieu_de' => $request->tieu_de,
        'mo_ta' => $request->mo_ta,
        'thoi_luong' => $request->thoi_luong,
        'danh_muc_id' => $request->danh_muc_id,
        'ngon_ngu_id' => $request->ngon_ngu_id,
        'poster' => $posterPath
    ]);

    return redirect()->route('admin.phim.index')->with('success', 'Thêm phim thành công!');
}


    // Hiển thị form chỉnh sửa phim
    public function edit($id)
{
    $phim = Phim::findOrFail($id);
    $danhMucs = DanhMuc::all();
    $ngonNgus = NgonNgu::all();
    $theLoais = TheLoai::all();   // ✅ thêm dòng này
    $dinhDangs = DinhDang::all(); // ✅ thêm nếu form có chọn định dạng

    return view('admin.phim.edit', compact('phim', 'danhMucs', 'ngonNgus', 'theLoais', 'dinhDangs'));
}


    // Cập nhật thông tin phim
    public function update(Request $request, $id)
    {
        $phim = Phim::findOrFail($id);

        $phim->update($request->all());
        $phim = Phim::findOrFail($id);
        $phim->theLoais()->sync($request->the_loai_id);
        $phim->dinhDangs()->sync($request->dinh_dang_id);

        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'thoi_luong' => 'required|numeric|min:1',
            'danh_muc_id' => 'required|exists:danh_muc,id',
            'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('poster')) {
            // Xóa poster cũ nếu có
            if ($phim->poster && Storage::disk('public')->exists($phim->poster)) {
                Storage::disk('public')->delete($phim->poster);
            }

            $posterPath = $request->file('poster')->store('posters', 'public');
            $phim->poster = $posterPath;
        }

        $phim->tieu_de = $request->tieu_de;
        $phim->mo_ta = $request->mo_ta;
        $phim->thoi_luong = $request->thoi_luong;
        $phim->danh_muc_id = $request->danh_muc_id;
        $phim->ngon_ngu_id = $request->ngon_ngu_id;
        $phim->save();

        return redirect()->route('admin.phim.index')->with('success', 'Cập nhật phim thành công!');
    }

    // Xóa phim
    public function destroy($id)
    {
        $phim = Phim::findOrFail($id);

        if ($phim->poster && Storage::disk('public')->exists($phim->poster)) {
            Storage::disk('public')->delete($phim->poster);
        }

        $phim->delete();

        return redirect()->route('admin.phim.index')->with('success', 'Xóa phim thành công!');
    }
}
