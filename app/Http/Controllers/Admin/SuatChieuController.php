<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuatChieu;
use App\Models\Phim;
use App\Models\PhongChieu;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
class SuatChieuController extends Controller
{
    public function index(Request $request)
    {
        $query = SuatChieu::with(['phim', 'phong']);

        // 🔍 Lọc theo tên phim
        if ($request->filled('q')) {
            $query->whereHas('phim', function ($q) use ($request) {
                $q->where('tieu_de', 'like', '%' . $request->q . '%');
            });
        }

        // 📅 Lọc theo ngày chiếu (nếu bạn có cột ngay_chieu)
        if ($request->filled('ngay_chieu')) {
            $query->whereDate('gio_bat_dau', $request->ngay_chieu);
        }

        // 🏠 Lọc theo phòng chiếu
        if ($request->filled('phong_id')) {
            $query->where('phong_id', $request->phong_id);
        }

        // ⚙️ Lọc theo trạng thái (nếu có cột trang_thai)
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // 🔄 Sắp xếp
        switch ($request->sort) {
            case 'time_asc':
                $query->orderBy('gio_bat_dau', 'asc');
                break;
            case 'time_desc':
                $query->orderBy('gio_bat_dau', 'desc');
                break;
            case 'movie_asc':
                $query->join('phim', 'suat_chieu.phim_id', '=', 'phim.id')
                      ->orderBy('phim.tieu_de', 'asc')
                      ->select('suat_chieu.*');
                break;
            case 'movie_desc':
                $query->join('phim', 'suat_chieu.phim_id', '=', 'phim.id')
                      ->orderBy('phim.tieu_de', 'desc')
                      ->select('suat_chieu.*');
                break;
            default:
                $query->orderBy('gio_bat_dau', 'asc');
                break;
        }

        $suatchieus = $query->paginate(10);
        $phongs = PhongChieu::orderBy('ten')->get();

        return view('admin.suatchieu.index', compact('suatchieus', 'phongs'));
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
    $suatchieu = SuatChieu::with(['phong.ghe'])->findOrFail($id);

    // ✅ Lấy danh sách ghế trong phòng chiếu
    $ghes = $suatchieu->phong->ghe()
        ->orderBy('hang')
        ->orderBy('cot')
        ->get()
        ->groupBy('hang');

    // 🔍 Lấy danh sách ghế đã đặt trong suất chiếu này
    // $gheDaDat = DB::table('ve')
    //     ->where('suat_chieu_id', $id)
    //     ->pluck('ghe_id')
    //     ->toArray();

    // 🔍 Lấy danh sách ghế đang giữ tạm (nếu có)
    $giuTamIds = DB::table('ghe_giu_tam')
        ->where('suat_chieu_id', $id)
        ->pluck('ghe_id')
        ->toArray();

    return view('admin.suatchieu.ghe_index', compact('suatchieu', 'ghes', 'giuTamIds'));
}
    
public function autoStore(Request $request)
{
    $validated = $request->validate([
        'phim_id' => 'required|exists:phim,id',
        'phong_id' => 'required|exists:phong_chieu,id',
        'ngay_bat_dau' => 'required|date',
        'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
        'gio_bat_dau_ngay' => 'required|date_format:H:i',
        'gia_ve' => 'required|numeric|min:0',
    ]);

    $phim = \App\Models\Phim::findOrFail($validated['phim_id']);
    $thoiLuongPhim = $phim->thoi_luong ?? 120; // phút
    $khoangNghi = 15; // phút giữa các suất

    $period = CarbonPeriod::create($validated['ngay_bat_dau'], $validated['ngay_ket_thuc']);
    $tongSuatTao = 0;

    foreach ($period as $ngay) {
        $gioBatDau = Carbon::parse($ngay->format('Y-m-d') . ' ' . $validated['gio_bat_dau_ngay']);

        while ($gioBatDau->hour < 23) {
            $gioKetThuc = (clone $gioBatDau)->addMinutes($thoiLuongPhim);

            // Nếu phim chiếu vượt qua 23h thì bỏ
            if ($gioKetThuc->hour >= 23) break;

            \App\Models\SuatChieu::create([
                'phim_id' => $validated['phim_id'],
                'phong_id' => $validated['phong_id'],
                'gio_bat_dau' => $gioBatDau,
                'gio_ket_thuc' => $gioKetThuc,
                'gia_ve' => $validated['gia_ve'],
            ]);

            $tongSuatTao++;
            $gioBatDau = $gioKetThuc->addMinutes($khoangNghi);
        }
    }

    return redirect()->route('admin.suatchieu.index')
        ->with('success', "Đã tự động tạo {$tongSuatTao} suất chiếu thành công!");
}
}
