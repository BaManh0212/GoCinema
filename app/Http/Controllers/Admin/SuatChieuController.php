<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuatChieu;
use App\Models\Phim;
use App\Models\PhongChieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SuatChieuController extends Controller
{
    /**
     * 🧭 Danh sách suất chiếu + bộ lọc
     */
    public function index(Request $request)
    {
        $query = SuatChieu::with(['phim', 'phong']);

        // 🔍 Lọc theo tên phim
        if ($request->filled('q')) {
            $query->whereHas('phim', function ($q) use ($request) {
                $q->where('tieu_de', 'like', '%' . $request->q . '%');
            });
        }

        // 📅 Lọc theo ngày
        if ($request->filled('ngay_chieu')) {
            $query->whereDate('gio_bat_dau', $request->ngay_chieu);
        }

        // 🏠 Lọc theo phòng
        if ($request->filled('phong_id')) {
            $query->where('phong_id', $request->phong_id);
        }

        // ⚙️ Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // 🔄 Sắp xếp
        switch ($request->sort) {
            case 'time_desc':
                $query->orderBy('gio_bat_dau', 'desc');
                break;
            case 'movie_asc':
                $query->join('phim', 'phim.id', '=', 'suat_chieu.phim_id')
                      ->orderBy('phim.tieu_de', 'asc')
                      ->select('suat_chieu.*');
                break;
            case 'movie_desc':
                $query->join('phim', 'phim.id', '=', 'suat_chieu.phim_id')
                      ->orderBy('phim.tieu_de', 'desc')
                      ->select('suat_chieu.*');
                break;
            default:
                $query->orderBy('gio_bat_dau', 'asc');
        }

        $suatchieus = $query->paginate(10);
        $phims = Phim::orderBy('tieu_de')->get();
        $phongs = PhongChieu::orderBy('ten')->get();

        return view('admin.suatchieu.index', compact('suatchieus', 'phims', 'phongs'));
    }

    /**
     * ➕ Trang thêm suất chiếu
     */
    public function create()
    {
        return view('admin.suatchieu.create', [
            'phims' => Phim::orderBy('tieu_de')->get(),
            'phongs' => PhongChieu::orderBy('ten')->get(),
        ]);
    }

    /**
     * 💾 Lưu suất chiếu mới
     */
    public function store(Request $request)
{
    // 🎬 Validate phim
    $phim_id = $request->input('phim_id');
    if (!$phim_id || !Phim::find($phim_id)) {
        return back()->withInput()->with('error', '⚠️ Vui lòng chọn phim hợp lệ.');
    }

    // 🏠 Validate phòng
    $phong_id = $request->input('phong_id');
    if (!$phong_id || !PhongChieu::find($phong_id)) {
        return back()->withInput()->with('error', '⚠️ Vui lòng chọn phòng hợp lệ.');
    }

    // 📅 Validate ngày bắt đầu
    try {
        $gio_bat_dau = Carbon::parse($request->input('gio_bat_dau'));
    } catch (\Exception $e) {
        return back()->withInput()->with('error', '⚠️ Ngày/giờ bắt đầu không hợp lệ.');
    }

    // 📅 Validate ngày kết thúc
    try {
        $gio_ket_thuc = Carbon::parse($request->input('gio_ket_thuc'));
    } catch (\Exception $e) {
        return back()->withInput()->with('error', '⚠️ Ngày/giờ kết thúc không hợp lệ.');
    }

    if ($gio_ket_thuc->lt($gio_bat_dau)) {
        return back()->withInput()->with('error', '❌ Ngày/giờ kết thúc phải sau ngày/giờ bắt đầu.');
    }

    // 💰 Validate giá vé
    $gia_ve = $request->input('gia_ve');
    if (!is_numeric($gia_ve) || $gia_ve < 0) {
        return back()->withInput()->with('error', '⚠️ Giá vé phải là số lớn hơn hoặc bằng 0.');
    }

    // 🎯 Kiểm tra trùng thời gian chiếu trong cùng phòng
    $trung = SuatChieu::where('phong_id', $phong_id)
        ->where(function ($query) use ($gio_bat_dau, $gio_ket_thuc) {
            $query->whereBetween('gio_bat_dau', [$gio_bat_dau, $gio_ket_thuc])
                  ->orWhereBetween('gio_ket_thuc', [$gio_bat_dau, $gio_ket_thuc])
                  ->orWhere(function ($q) use ($gio_bat_dau, $gio_ket_thuc) {
                      $q->where('gio_bat_dau', '<=', $gio_bat_dau)
                        ->where('gio_ket_thuc', '>=', $gio_ket_thuc);
                  });
        })
        ->exists();

    if ($trung) {
        return back()->withInput()->with('error', '⚠️ Thời gian chiếu bị trùng với một suất chiếu khác trong cùng phòng!');
    }

    // Nếu không trùng, lưu suất chiếu
    SuatChieu::create([
        'phim_id' => $phim_id,
        'phong_id' => $phong_id,
        'gio_bat_dau' => $gio_bat_dau,
        'gio_ket_thuc' => $gio_ket_thuc,
        'gia_ve' => $gia_ve,
        'trang_thai' => 'hoat_dong',
    ]);

    return redirect()->route('admin.suatchieu.index')
                     ->with('success', '🎉 Thêm suất chiếu thành công!');
}

    /**
     * ✏️ Trang sửa
     */
    public function edit($id)
    {
        $suatchieu = SuatChieu::findOrFail($id);

        return view('admin.suatchieu.edit', [
            'suatchieu' => $suatchieu,
            'phims' => Phim::orderBy('tieu_de')->get(),
            'phongs' => PhongChieu::orderBy('ten')->get(),
        ]);
    }

    /**
     * 🔄 Cập nhật suất chiếu
     */
    public function update(Request $request, $id)
{
    $suatchieu = SuatChieu::findOrFail($id);

    // Kiểm tra nếu suất chiếu đã bắt đầu hoặc kết thúc
    if (Carbon::now()->gte($suatchieu->gio_bat_dau)) {
        return back()->with('error', '⚠️ Không thể sửa suất chiếu đã bắt đầu hoặc đã kết thúc.');
    }

    // --- Tiếp tục validate và update như bình thường ---
    $phim_id = $request->input('phim_id');
    if (!$phim_id || !Phim::find($phim_id)) {
        return back()->withInput()->with('error', '⚠️ Vui lòng chọn phim hợp lệ.');
    }

    $phong_id = $request->input('phong_id');
    if (!$phong_id || !PhongChieu::find($phong_id)) {
        return back()->withInput()->with('error', '⚠️ Vui lòng chọn phòng hợp lệ.');
    }

    try {
        $gio_bat_dau = Carbon::parse($request->input('gio_bat_dau'));
        $gio_ket_thuc = Carbon::parse($request->input('gio_ket_thuc'));
    } catch (\Exception $e) {
        return back()->withInput()->with('error', '⚠️ Ngày/giờ không hợp lệ.');
    }

    if ($gio_ket_thuc->lt($gio_bat_dau)) {
        return back()->withInput()->with('error', '❌ Ngày/giờ kết thúc phải sau ngày/giờ bắt đầu.');
    }

    $gia_ve = $request->input('gia_ve');
    if (!is_numeric($gia_ve) || $gia_ve < 0) {
        return back()->withInput()->with('error', '⚠️ Giá vé phải là số lớn hơn hoặc bằng 0.');
    }

    // Kiểm tra trùng suất (bỏ qua chính suất đang update)
    $trung = SuatChieu::where('phong_id', $phong_id)
        ->where('id', '!=', $suatchieu->id)
        ->where(function ($query) use ($gio_bat_dau, $gio_ket_thuc) {
            $query->whereBetween('gio_bat_dau', [$gio_bat_dau, $gio_ket_thuc])
                  ->orWhereBetween('gio_ket_thuc', [$gio_bat_dau, $gio_ket_thuc])
                  ->orWhere(function ($q) use ($gio_bat_dau, $gio_ket_thuc) {
                      $q->where('gio_bat_dau', '<=', $gio_bat_dau)
                        ->where('gio_ket_thuc', '>=', $gio_ket_thuc);
                  });
        })
        ->exists();

    if ($trung) {
        return back()->withInput()->with('error', '⚠️ Thời gian chiếu bị trùng với một suất chiếu khác trong cùng phòng!');
    }

    // Cập nhật
    $suatchieu->update([
        'phim_id' => $phim_id,
        'phong_id' => $phong_id,
        'gio_bat_dau' => $gio_bat_dau,
        'gio_ket_thuc' => $gio_ket_thuc,
        'gia_ve' => $gia_ve,
    ]);

    return redirect()->route('admin.suatchieu.index')
                     ->with('success', '✅ Cập nhật suất chiếu thành công!');
}

    /**
     * ❌ Xóa suất chiếu
     */
    public function destroy($id)
{
    $suatchieu = SuatChieu::findOrFail($id);

    // Kiểm tra nếu suất chiếu đã bắt đầu
    if (Carbon::now()->gte($suatchieu->gio_bat_dau)) {
        return back()->with('error', '⚠️ Không thể xóa suất chiếu đã bắt đầu hoặc đã kết thúc.');
    }

    // Kiểm tra nếu suất chiếu đã có vé
    if ($suatchieu->chiTietVe()->exists()) {
        return back()->with('error', '⚠️ Suất chiếu đã có vé, không thể xóa. Vui lòng hủy suất chiếu nếu cần.');
    }

    $suatchieu->delete();

    return redirect()->route('admin.suatchieu.index')
                     ->with('success', '🗑️ Đã xóa suất chiếu!');
}


    /**
    * ⚙️ Tự động tạo suất chiếu nhiều ngày (Preview mode)
     */
   public function autoStore(Request $request)
{
    // 🎬 Validate phim
    $phim_id = $request->input('phim_id');
    $phim = Phim::find($phim_id);
    if (!$phim) return back()->withInput()->with('error', '⚠️ Vui lòng chọn phim hợp lệ.');

    // 🏠 Validate phòng
    $phong_id = $request->input('phong_id');
    $phong = PhongChieu::find($phong_id);
    if (!$phong) return back()->withInput()->with('error', '⚠️ Vui lòng chọn phòng hợp lệ.');

    // 📅 Validate ngày
    try {
        $ngay_bat_dau = Carbon::parse($request->input('ngay_bat_dau'));
        $ngay_ket_thuc = Carbon::parse($request->input('ngay_ket_thuc'));
    } catch (\Exception $e) {
        return back()->withInput()->with('error', '⚠️ Ngày không hợp lệ.');
    }
    if ($ngay_ket_thuc->lt($ngay_bat_dau)) {
        return back()->withInput()->with('error', '❌ Ngày kết thúc phải sau ngày bắt đầu.');
    }

    // ⏰ Thời gian bắt đầu
    $gio_bat_dau_ngay = $request->input('gio_bat_dau_ngay');
    if (!preg_match('/^\d{2}:\d{2}$/', $gio_bat_dau_ngay)) {
        return back()->withInput()->with('error', '⚠️ Giờ bắt đầu không hợp lệ.');
    }

    // 💰 Giá vé
    $gia_ve = $request->input('gia_ve') ?? 70000;

    // ⏱️ Thời lượng + thời gian dọn phòng
    $thoiLuong = $phim->thoi_luong ?? 120;
    $khoangNghi = 15;

    // 🕒 Giờ cố định (nếu chọn)
    $gioCoDinh = $request->input('gio_co_dinh', []);

    $period = CarbonPeriod::create($ngay_bat_dau, $ngay_ket_thuc);
    $preview = []; // Mảng chứa suất đề xuất

    foreach ($period as $ngay) {
        // 1️⃣ Nếu có chọn giờ cố định, tạo theo từng giờ
        if (!empty($gioCoDinh)) {
            foreach ($gioCoDinh as $gio) {
                $start = Carbon::parse($ngay->format('Y-m-d') . ' ' . $gio);
                $end = (clone $start)->addMinutes($thoiLuong + $khoangNghi);

                // Kiểm tra trùng với DB
                $conflict = SuatChieu::where('phong_id', $phong_id)
                    ->where('gio_bat_dau', '<', $end)
                    ->where('gio_ket_thuc', '>', $start)
                    ->exists();

                $preview[] = [
                    'phim_id' => $phim_id,
                    'phong_id' => $phong_id,
                    'gio_bat_dau' => $start->toDateTimeString(),
                    'gio_ket_thuc' => $end->toDateTimeString(),
                    'gia_ve' => $gia_ve,
                    'trang_thai' => 'hoat_dong',
                    'conflict' => $conflict,
                    'phim_ten' => $phim->tieu_de,
                    'phong_ten' => $phong->ten,
                ];
            }
        } else {
            // 2️⃣ Nếu nhập giờ đầu tiên, tạo liên tục theo thời lượng + dọn phòng
            $start = Carbon::parse($ngay->format('Y-m-d') . ' ' . $gio_bat_dau_ngay);

            while ($start->hour < 23) {
                $end = (clone $start)->addMinutes($thoiLuong);
                if ($end->hour >= 23) break;

                // Kiểm tra trùng với DB
                $conflict = SuatChieu::where('phong_id', $phong_id)
                    ->where('gio_bat_dau', '<', $end)
                    ->where('gio_ket_thuc', '>', $start)
                    ->exists();

                $preview[] = [
                    'phim_id' => $phim_id,
                    'phong_id' => $phong_id,
                    'gio_bat_dau' => $start->toDateTimeString(),
                    'gio_ket_thuc' => $end->toDateTimeString(),
                    'gia_ve' => $gia_ve,
                    'trang_thai' => 'hoat_dong',
                    'conflict' => $conflict,
                    'phim_ten' => $phim->tieu_de,
                    'phong_ten' => $phong->ten,
                ];

                $start = $end->addMinutes($khoangNghi);
            }
        }
    }

    // Lưu preview vào session
    session(['suatchieu_preview' => $preview]);

    return redirect()->back()->withInput()->with('preview', $preview);
}



    /**
     * 🧩 Cập nhật trạng thái hàng loạt
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ngay' => 'required|date',
            'trang_thai' => 'required|in:hoat_dong,tam_dung,huy',
            'ly_do_huy' => 'nullable|string|max:255',
        ]);

        // Không cho phép bật lại suất đã hủy
        if ($request->trang_thai === 'hoat_dong') {
            $daHuy = SuatChieu::whereDate('gio_bat_dau', $request->ngay)
                ->where('trang_thai', 'huy')
                ->count();

            if ($daHuy > 0) {
                return back()->withErrors([
                    'error' => "⚠️ Có {$daHuy} suất đã bị hủy — không thể chuyển lại sang hoạt động!"
                ]);
            }
        }

        $query = SuatChieu::whereDate('gio_bat_dau', $request->ngay);
        if ($request->filled('phong_id')) $query->where('phong_id', $request->phong_id);
        if ($request->filled('phim_id')) $query->where('phim_id', $request->phim_id);

        $affected = $query->update([
            'trang_thai' => $request->trang_thai,
            'ly_do_huy' => $request->ly_do_huy,
        ]);

        return back()->with('success', "✅ Đã cập nhật {$affected} suất chiếu trong ngày {$request->ngay}.");
    }

    /**
     * 💾 Lưu preview suất chiếu vào DB
     */
    public function storePreview(Request $request)
    {
        $previewJson = $request->input('preview_data');
        if (!$previewJson) {
            return back()->with('error', '⚠️ Không có dữ liệu preview để lưu.');
        }

        $preview = json_decode($previewJson, true);
        if (!$preview) {
            return back()->with('error', '⚠️ Dữ liệu preview không hợp lệ.');
        }

        $saved = 0;
        $skipped = 0;

        foreach ($preview as $suat) {
            if (!$suat['conflict']) {
                SuatChieu::create([
                    'phim_id' => $suat['phim_id'],
                    'phong_id' => $suat['phong_id'],
                    'gio_bat_dau' => $suat['gio_bat_dau'],
                    'gio_ket_thuc' => $suat['gio_ket_thuc'],
                    'gia_ve' => $suat['gia_ve'],
                    'trang_thai' => $suat['trang_thai'],
                ]);
                $saved++;
            } else {
                $skipped++;
            }
        }

        // Xóa session sau khi lưu
        session()->forget('suatchieu_preview');

        $msg = "🚀 Đã lưu {$saved} suất chiếu thành công.";
        if ($skipped > 0) {
            $msg .= " Bỏ qua {$skipped} suất do trùng phòng hoặc giờ .";
        }

        return redirect()->route('admin.suatchieu.index')->with('success', $msg);
    }

    /**
     * 🔄 Cập nhật trạng thái đơn lẻ
     */
    public function updateTrangThai(Request $request, $id)
{
    $suatChieu = SuatChieu::findOrFail($id);

    $request->validate([
        'trang_thai' => 'required|in:hoat_dong,tam_dung,huy',
    ]);

    $oldStatus = $suatChieu->trang_thai;
    $suatChieu->trang_thai = $request->trang_thai;

    if ($oldStatus === $suatChieu->trang_thai) {
        return back()->with('error', '⚠️ Trạng thái không thay đổi.');
    }

    $suatChieu->save();

    return back()->with('success', '✅ Cập nhật trạng thái suất chiếu thành công!');
}
public function showAdmin($suatChieuId)
{
    // Lấy suất chiếu theo ID
    $suatChieu = SuatChieu::with('phong.soDoGhe')->findOrFail($suatChieuId);
    $phong = $suatChieu->phong;

    // Kiểm tra phòng và sơ đồ ghế
    if (!$phong || !$phong->soDoGhe) {
        return back()->with('error', 'Phòng này chưa có sơ đồ ghế!');
    }

    // Lấy ma trận ghế từ JSON
    $matrix = json_decode($phong->soDoGhe->ma_tran, true) ?: [];

    // Lấy trạng thái ghế đã đặt
    $trangThaiGhe = DB::table('ghe_suat_chieu')
        ->where('suat_chieu_id', $suatChieu->id)
        ->pluck('trang_thai', 'ghe_id');

    // Truyền sang view admin
    return view('admin.suatchieu.show', compact(
        'suatChieu', 'phong', 'matrix', 'trangThaiGhe'
    ));
}


}
