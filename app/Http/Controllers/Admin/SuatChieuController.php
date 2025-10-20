<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuatChieu;
use App\Models\Phim;
use App\Models\PhongChieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuatChieuController extends Controller
{
    public function index()
    {
        $suatchieus = SuatChieu::with(['phim', 'phong'])
            ->orderBy('gio_bat_dau', 'asc')
            ->paginate(10);

        return view('admin.suatchieu.index', compact('suatchieus'));
    }

    public function create()
    {
        $phims = Phim::orderBy('tieu_de', 'asc')->get();
        $phongs = PhongChieu::orderBy('ten')->get();
        return view('admin.suatchieu.create', compact('phims', 'phongs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phim_id' => 'required|exists:phim,id',
            'phong_id' => 'required|exists:phong_chieu,id',
            'gio_bat_dau' => 'required|date',
            'gio_ket_thuc' => 'required|date|after:gio_bat_dau',
            'gia_ve' => 'required|numeric|min:0',
        ], [
            'phim_id.required' => 'Vui lòng chọn phim.',
            'phong_id.required' => 'Vui lòng chọn phòng chiếu.',
            'gio_bat_dau.required' => 'Vui lòng chọn giờ bắt đầu.',
            'gio_ket_thuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'gia_ve.required' => 'Vui lòng nhập giá vé.',
        ]);

        try {
            SuatChieu::create($validated);
            return redirect()->route('admin.suatchieu.index')
                ->with('success', 'Thêm suất chiếu thành công');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $suatchieu = SuatChieu::findOrFail($id);
        $phims = Phim::orderBy('tieu_de', 'asc')->get();
        $phongs = PhongChieu::orderBy('ten')->get();
        return view('admin.suatchieu.edit', compact('suatchieu', 'phims', 'phongs'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'phim_id' => 'required|exists:phim,id',
            'phong_id' => 'required|exists:phong_chieu,id',
            'gio_bat_dau' => 'required|date',
            'gio_ket_thuc' => 'required|date|after:gio_bat_dau',
            'gia_ve' => 'required|numeric|min:0',
        ]);

        try {
            $suatchieu = SuatChieu::findOrFail($id);
            $suatchieu->update($validated);
            return redirect()->route('admin.suatchieu.index')
                ->with('success', 'Cập nhật suất chiếu thành công');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $suatchieu = SuatChieu::findOrFail($id);
            $suatchieu->delete();
            return redirect()->route('admin.suatchieu.index')
                ->with('success', 'Xóa suất chiếu thành công');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
    public function gheIndex($id)
    {
    $suatchieu = SuatChieu::with('phong.ghe')->findOrFail($id);

    // Lấy danh sách ghế theo phòng
    $ghes = $suatchieu->phong->ghe()
        ->orderBy('hang')
        ->orderBy('cot')
        ->get()
        ->groupBy('hang');

    // Lấy danh sách ghế đang giữ tạm
   // Tạm thời chưa có ghe_giu_tam
    $giuTamIds = []; // sẽ chứa id các ghế đang giữ sau này


    return view('admin.suatchieu.ghe_index', compact('suatchieu', 'ghes', 'giuTamIds'));
    }

}
