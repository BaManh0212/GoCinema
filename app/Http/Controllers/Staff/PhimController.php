<?php

namespace App\Http\Controllers\Staff;

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
        $phims = Phim::with(['danhMucs', 'ngonNgu'])->paginate(10);
        return view('staff.phim.index', compact('phims'));
    }

    public function trashed()
    {
        $phims = Phim::onlyTrashed()->with(['danhMucs', 'ngonNgu'])->paginate(10);
        return view('staff.phim.trashed', compact('phims'));
    }

    public function create()
    {
        $danhMucs = DanhMuc::all();
        $ngonNgus = NgonNgu::all();
        return view('staff.phim.create', compact('danhMucs', 'ngonNgus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255|unique:phim,tieu_de',
            'mo_ta' => 'nullable|string',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string',
            'thoi_luong' => 'required|numeric|min:1',
            'danh_muc_ids' => 'required|array',
            'danh_muc_ids.*' => 'exists:danh_muc,id',
            'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
            'trailer' => 'nullable|string|max:255',
            'phu_de' => 'boolean',
            'ngay_cong_chieu' => 'required|date',
            'do_tuoi_gioi_han' => 'nullable|string|max:10',
            'anh_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tieu_de.required' => 'Vui lòng nhập tiêu đề phim.',
            'tieu_de.unique' => 'Tiêu đề phim đã tồn tại.',
            'tieu_de.max' => 'Tiêu đề phim không được vượt quá 255 ký tự.',
            'dao_dien.required' => 'Vui lòng nhập tên đạo diễn.',
            'dao_dien.max' => 'Tên đạo diễn không được vượt quá 255 ký tự.',
            'dien_vien.required' => 'Vui lòng nhập tên diễn viên.',
            'thoi_luong.required' => 'Vui lòng nhập thời lượng phim.',
            'thoi_luong.numeric' => 'Thời lượng phải là số.',
            'thoi_luong.min' => 'Thời lượng phải lớn hơn 0.',
            'danh_muc_ids.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'danh_muc_ids.*.exists' => 'Danh mục không hợp lệ.',
            'ngon_ngu_id.required' => 'Vui lòng chọn ngôn ngữ.',
            'ngon_ngu_id.exists' => 'Ngôn ngữ không hợp lệ.',
            'ngay_cong_chieu.required' => 'Vui lòng chọn ngày công chiếu.',
            'anh_poster.image' => 'File tải lên phải là hình ảnh.',
            'anh_poster.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
            'anh_poster.max' => 'Ảnh không được vượt quá 2MB.',
        ]);

        $posterPath = null;
        if ($request->hasFile('anh_poster')) {
            $posterPath = $request->file('anh_poster')->store('posters', 'public');
        }

        $phim = Phim::create([
            'tieu_de' => $validated['tieu_de'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'thoi_luong' => $validated['thoi_luong'],
            'ngon_ngu_id' => $validated['ngon_ngu_id'],
            'anh_poster' => $posterPath,
            'trailer' => $validated['trailer'] ?? null,
            'phu_de' => $validated['phu_de'] ?? false,
            'ngay_cong_chieu' => $validated['ngay_cong_chieu'],
            'do_tuoi_gioi_han' => $validated['do_tuoi_gioi_han'] ?? null,
            'dao_dien' => $validated['dao_dien'],
            'dien_vien' => $validated['dien_vien'],
        ]);

        $phim->danhMucs()->sync($validated['danh_muc_ids']);

        return redirect()->route('staff.phim.index')->with('success', '🎬 Thêm phim thành công!');
    }

    public function edit($id)
    {
        $phim = Phim::with('danhMucs')->findOrFail($id);
        $danhMucs = DanhMuc::all();
        $ngonNgus = NgonNgu::all();
        return view('staff.phim.edit', compact('phim', 'danhMucs', 'ngonNgus'));
    }

    public function update(Request $request, $id)
    {
        $phim = Phim::findOrFail($id);

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255|unique:phim,tieu_de,' . $phim->id,
            'mo_ta' => 'nullable|string',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string',
            'trailer' => 'nullable|url',
            'phu_de' => 'required|boolean',
            'thoi_luong' => 'required|integer|min:1',
            'ngay_cong_chieu' => 'required|date',
            'do_tuoi_gioi_han' => 'nullable|string|max:10',
            'danh_muc_ids' => 'required|array',
            'danh_muc_ids.*' => 'exists:danh_muc,id',
            'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
            'anh_poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'tieu_de.required' => 'Vui lòng nhập tiêu đề phim.',
            'tieu_de.unique' => 'Tiêu đề phim đã tồn tại.',
            'dao_dien.required' => 'Vui lòng nhập tên đạo diễn.',
            'dien_vien.required' => 'Vui lòng nhập tên diễn viên.',
            'phu_de.required' => 'Vui lòng chọn phụ đề.',
            'thoi_luong.required' => 'Vui lòng nhập thời lượng phim.',
            'thoi_luong.min' => 'Thời lượng phải lớn hơn 0.',
            'ngay_cong_chieu.required' => 'Vui lòng chọn ngày công chiếu.',
            'danh_muc_ids.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'danh_muc_ids.*.exists' => 'Danh mục không hợp lệ.',
            'ngon_ngu_id.required' => 'Vui lòng chọn ngôn ngữ.',
            'ngon_ngu_id.exists' => 'Ngôn ngữ không hợp lệ.',
        ]);

        if ($request->hasFile('anh_poster')) {
            if ($phim->anh_poster) {
                Storage::disk('public')->delete($phim->anh_poster);
            }
            $validated['anh_poster'] = $request->file('anh_poster')->store('posters', 'public');
        }

        $phim->update($validated);
        $phim->danhMucs()->sync($validated['danh_muc_ids']);

        return redirect()->route('staff.phim.index')->with('success', '🎬 Cập nhật phim thành công!');
    }

    public function show($id)
    {
        $phim = Phim::with(['danhMucs', 'ngonNgu'])->findOrFail($id);
        return view('staff.phim.show', compact('phim'));
    }

    public function destroy($id)
    {
        $phim = Phim::findOrFail($id);
        $phim->delete();
        return redirect()->route('staff.phim.index')->with('success', 'Đã xóa phim!');
    }

    public function restore($id)
    {
        $phim = Phim::withTrashed()->findOrFail($id);
        $phim->restore();
        return redirect()->route('staff.phim.index')->with('success', 'Khôi phục phim thành công!');
    }

    public function forceDelete($id)
    {
        $phim = Phim::withTrashed()->findOrFail($id);
        if ($phim->anh_poster && Storage::disk('public')->exists($phim->anh_poster)) {
            Storage::disk('public')->delete($phim->anh_poster);
        }
        $phim->forceDelete();
        return redirect()->route('staff.phim.trashed')->with('success', 'Đã xóa vĩnh viễn phim.');
    }
}
