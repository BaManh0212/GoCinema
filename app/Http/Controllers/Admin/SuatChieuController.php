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
     * 🎟️ Xem sơ đồ ghế
     */
    public function gheIndex($id)
    {
        $suatchieu = SuatChieu::with(['phong.ghe'])->findOrFail($id);

        $ghes = $suatchieu->phong->ghe()
            ->orderBy('hang')->orderBy('cot')->get()
            ->groupBy('hang');

        $giuTamIds = DB::table('ghe_giu_tam')
            ->where('suat_chieu_id', $id)
            ->pluck('ghe_id')->toArray();

        return view('admin.suatchieu.ghe_index', compact('suatchieu', 'ghes', 'giuTamIds'));
    }

    /**
     * ⚙️ Tự động tạo suất chiếu nhiều ngày
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
    $conflicts = [];
    $tongSuat = 0;

    foreach ($period as $ngay) {
        // 1️⃣ Nếu có chọn giờ cố định, tạo theo từng giờ
        if (!empty($gioCoDinh)) {
            foreach ($gioCoDinh as $gio) {
                $start = Carbon::parse($ngay->format('Y-m-d') . ' ' . $gio);
                $end = (clone $start)->addMinutes($thoiLuong);

                // Kiểm tra trùng
                $exists = SuatChieu::where('phong_id', $phong_id)
                    ->where('gio_bat_dau', '<', $end)
                    ->where('gio_ket_thuc', '>', $start)
                    ->exists();

                if ($exists) {
                    $conflicts[] = $start->format('d/m/Y H:i') . ' - ' . $end->format('H:i');
                    continue;
                }

                SuatChieu::create([
                    'phim_id' => $phim_id,
                    'phong_id' => $phong_id,
                    'gio_bat_dau' => $start,
                    'gio_ket_thuc' => $end,
                    'gia_ve' => $gia_ve,
                    'trang_thai' => 'hoat_dong',
                ]);
                $tongSuat++;
            }
        } else {
            // 2️⃣ Nếu nhập giờ đầu tiên, tạo liên tục theo thời lượng + dọn phòng
            $start = Carbon::parse($ngay->format('Y-m-d') . ' ' . $gio_bat_dau_ngay);

            while ($start->hour < 23) {
                $end = (clone $start)->addMinutes($thoiLuong);
                if ($end->hour >= 23) break;

                // Kiểm tra trùng
                $exists = SuatChieu::where('phong_id', $phong_id)
                    ->where('gio_bat_dau', '<', $end)
                    ->where('gio_ket_thuc', '>', $start)
                    ->exists();

                if ($exists) {
                    $conflicts[] = $start->format('d/m/Y H:i') . ' - ' . $end->format('H:i');
                    $start = $end->addMinutes($khoangNghi);
                    continue;
                }

                SuatChieu::create([
                    'phim_id' => $phim_id,
                    'phong_id' => $phong_id,
                    'gio_bat_dau' => $start,
                    'gio_ket_thuc' => $end,
                    'gia_ve' => $gia_ve,
                    'trang_thai' => 'hoat_dong',
                ]);
                $tongSuat++;
                $start = $end->addMinutes($khoangNghi);
            }
        }
    }

    if (!empty($conflicts)) {
        $msg = "❌ Một số suất chiếu không tạo được do trùng: " . implode(', ', array_slice($conflicts, 0, 5));
        if (count($conflicts) > 5) $msg .= ', ...';
        return back()->with('success', "🚀 Đã tạo {$tongSuat} suất chiếu thành công. {$msg}");
    }

    return redirect()->route('admin.suatchieu.index')
                     ->with('success', "🚀 Đã tạo {$tongSuat} suất chiếu thành công!");
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

}
