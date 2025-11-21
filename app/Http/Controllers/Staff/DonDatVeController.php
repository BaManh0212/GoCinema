<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use App\Models\Phim;
use App\Models\SuatChieu;
use App\Models\Ghe;
use App\Models\ChiTietVe;
use App\Models\GheSuatChieu;
use App\Models\Combo;
use Barryvdh\DomPDF\Facade\Pdf; // cần cài DomPDF
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonDatVeController extends Controller
{
    /**
     * Hiển thị danh sách đơn đặt vé
     */
    public function index(Request $request)
    {
        $query = DonDatVe::with(['nguoiDung', 'suatChieu.phim', 'suatChieu.phongChieu']);

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $donDatVes = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('staff.donve.index', compact('donDatVes'));
    }

    /**
     * Xem chi tiết đơn đặt vé
     */
    public function show($id)
    {
        $donVe = DonDatVe::with([
            'nguoiDung',
            'suatChieu.phim',
            'suatChieu.phongChieu',
            'chiTietVes.ghe'
        ])->findOrFail($id);

        return view('staff.donve.show', compact('donVe'));
    }

    /**
     * In vé (PDF)
     */
    public function print($id)
    {
        $donVe = DonDatVe::with([
            'nguoiDung',
            'suatChieu.phim',
            'suatChieu.phongChieu',
            'chiTietVes.ghe'
        ])->findOrFail($id);

        // Chỉ cho in nếu đơn đã thanh toán hoặc đã check-in
        $allowedToPrint = ['da_thanh_toan', 'da_checkin'];
        if (! in_array($donVe->trang_thai, $allowedToPrint)) {
            return redirect()->route('staff.donve.index')->withErrors(['error' => 'Chỉ có đơn đã thanh toán hoặc đã check-in mới được in vé.']);
        }

        $pdf = Pdf::loadView('staff.donve.print', compact('donVe'));
        return $pdf->stream("Ve_{$donVe->ma_don}.pdf");
    }

    /**
     * Thay đổi trạng thái đơn với logic chuyển trạng thái hợp lệ.
     * Nếu chuyển sai sẽ trả về lỗi (flash message) và không lưu.
     */
    public function changeStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'trang_thai' => 'required|in:cho_thanh_toan,da_thanh_toan,da_checkin,da_huy',
        ]);

        $donVe = DonDatVe::with('chiTietVes')->findOrFail($id);
        $current = $donVe->trang_thai;
        $target = $validated['trang_thai'];

        if ($current === $target) {
            return back()->with('info', 'Trạng thái không thay đổi.');
        }

        // Định nghĩa các chuyển trạng thái hợp lệ
        $allowed = [
            'cho_thanh_toan' => ['da_thanh_toan', 'da_huy'],
            'da_thanh_toan' => ['da_huy', 'da_checkin'], // cho phép hủy (hoàn tiền) hoặc check-in từ đã thanh toán
            'da_checkin' => [], // đã check-in, không cho chuyển tiếp (bạn có thể thay đổi nếu muốn)
            'da_huy' => [], // không thể chuyển tiếp
        ];

        if (!isset($allowed[$current])) {
            return back()->withErrors(['trang_thai' => 'Trạng thái hiện tại không cho phép thay đổi.']);
        }

        if (!in_array($target, $allowed[$current])) {
            return back()->withErrors(['trang_thai' => "Không thể đổi trạng thái từ '".str_replace('_',' ',$current)."' sang '".str_replace('_',' ',$target)."'."]);
        }

        // Thực hiện cập nhật trong transaction
        DB::beginTransaction();
        try {
            $donVe->trang_thai = $target;
            $donVe->save();

            // Đồng bộ trạng thái chi tiết vé khi cần
            if ($target === 'da_thanh_toan') {
                foreach ($donVe->chiTietVes as $ct) {
                    $ct->trang_thai = 'da_thanh_toan';
                    $ct->save();
                }
                // Trừ tồn kho combo khi đơn chuyển sang đã thanh toán
                $donVe->load('combos');
                foreach ($donVe->combos as $combo) {
                    $soLuongMua = (int) ($combo->pivot->so_luong ?? 0);
                    if ($soLuongMua <= 0) continue;
                    if ($combo->so_luong < $soLuongMua) {
                        throw new \Exception("Combo '{$combo->ten}' không đủ số lượng (cần {$soLuongMua}, còn {$combo->so_luong}).");
                    }
                    $combo->so_luong = $combo->so_luong - $soLuongMua;
                    $combo->save();
                }
            } elseif ($target === 'da_checkin') {
                foreach ($donVe->chiTietVes as $ct) {
                    $ct->trang_thai = 'da_su_dung';
                    $ct->save();
                }
            } elseif ($target === 'da_huy') {
                foreach ($donVe->chiTietVes as $ct) {
                    $ct->trang_thai = 'da_huy';
                    $ct->save();
                }
                // Hoàn kho combo nếu hủy từ trạng thái đã thanh toán
                if ($current === 'da_thanh_toan') {
                    $donVe->load('combos');
                    foreach ($donVe->combos as $combo) {
                        $soLuongMua = (int) ($combo->pivot->so_luong ?? 0);
                        if ($soLuongMua <= 0) continue;
                        $combo->so_luong = $combo->so_luong + $soLuongMua;
                        $combo->save();
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Lỗi khi thay đổi trạng thái DonDatVe: '.$e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi khi cập nhật trạng thái. Vui lòng thử lại.']);
        }
    }

    /**
 * Check-in bằng mã đơn (ma_don).
 * Yêu cầu: đơn phải ở trạng thái 'da_thanh_toan' mới được check-in.
 * Nếu thành công sẽ đánh dấu các ChiTietVe liên quan là 'da_su_dung'
 * và cập nhật trạng thái đơn thành 'da_checkin'.
 */
public function checkInByCode(Request $request)
{
    $validated = $request->validate([
        'ma_don' => 'required|string',
    ]);

    $maDon = trim($validated['ma_don']);
    $don = DonDatVe::with('chiTietVes')->where('ma_don', $maDon)->first();

    if (! $don) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Mã đơn không tồn tại.'], 404);
        }
        return back()->withErrors(['ma_don' => 'Mã đơn không tồn tại.']);
    }

    // Kiểm tra trạng thái thanh toán
    if ($don->trang_thai !== 'da_thanh_toan') {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Đơn chưa được thanh toán nên không thể check-in.'], 422);
        }
        return back()->withErrors(['ma_don' => 'Đơn chưa được thanh toán nên không thể check-in.']);
    }

    // Kiểm tra thời gian suất chiếu
    $now = now();
    $suatChieu = $don->suatChieu;

    if (!$suatChieu) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Không tìm thấy thông tin suất chiếu.'], 404);
        }
        return back()->withErrors(['ma_don' => 'Không tìm thấy thông tin suất chiếu.']);
    }

    $thoiGianBatDau = \Carbon\Carbon::parse($suatChieu->gio_bat_dau);
    $thoiGianBatDauCheckin = $thoiGianBatDau->copy()->subMinutes(45);  // Được phép check-in từ 45 phút trước
    $thoiGianKetThucCheckin = $thoiGianBatDau->copy()->subMinutes(10);  // Đến 10 phút trước khi phim bắt đầu

    // Kiểm tra thời gian check-in hợp lệ (từ 45p đến 10p trước khi phim bắt đầu)
    if ($now->lt($thoiGianBatDauCheckin)) {
        // Nếu còn sớm hơn thời gian bắt đầu cho phép check-in
        $thoiGianConLai = $now->diffForHumans($thoiGianBatDauCheckin, [
            'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
            'options' => \Carbon\CarbonInterface::JUST_NOW | \Carbon\CarbonInterface::ONE_DAY_WORDS | \Carbon\CarbonInterface::TWO_DAY_WORDS
        ]);
        $message = "Chỉ được phép check-in từ 45 phút đến 10 phút trước khi phim bắt đầu. Vui lòng quay lại sau $thoiGianConLai.";
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 400);
        }
        return back()->withErrors(['ma_don' => $message]);
    } elseif ($now->gt($thoiGianKetThucCheckin)) {
        // Nếu đã quá thời gian cho phép check-in (ít hơn 10 phút trước khi phim bắt đầu)
        $message = 'Đã quá thời gian cho phép check-in. Vui lòng liên hệ nhân viên để được hỗ trợ.';
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 400);
        }
        return back()->withErrors(['ma_don' => $message]);
    }

    // Nếu đã quá thời gian bắt đầu phim
    if ($now->gt($thoiGianBatDau)) {
        $message = 'Đã quá thời gian cho phép check-in. Vui lòng liên hệ quầy vé để được hỗ trợ.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }
        return back()->withErrors(['ma_don' => $message]);
    }

    DB::beginTransaction();
    try {
        // ✅ Cập nhật trạng thái chi tiết vé
        foreach ($don->chiTietVes as $ct) {
            if ($ct->trang_thai !== 'da_su_dung') {
                $ct->trang_thai = 'da_su_dung';
                $ct->save();
            }
        }

        // ✅ Cập nhật trạng thái đơn vé tổng
        $don->trang_thai = 'da_checkin';
        $don->save();

        DB::commit();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Check-in thành công.', 'ma_don' => $maDon]);
        }

        return back()->with('success', "✅ Check-in thành công cho mã đơn $maDon");
    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Lỗi khi check-in theo mã đơn: ' . $e->getMessage());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Có lỗi khi check-in.'], 500);
        }

        return back()->withErrors(['error' => 'Có lỗi khi check-in. Vui lòng thử lại.']);
    }
}


    /**
     * Hiển thị trang check-in (form nhập mã đơn) cho staff/staff
     */
    public function showCheckinForm(Request $request)
    {
        // Có thể truyền ma_don qua query string để tự điền
        $maDon = $request->query('ma_don', '');
        return view('staff.donve.checkin', compact('maDon'));
    }

    /**
     * Hiển thị form chọn phim để đặt vé tại quầy
     */
    public function create()
    {
        $phims = Phim::dangChieu()
            ->orderBy('ngay_cong_chieu', 'desc')
            ->get();

        return view('staff.donve.create', compact('phims'));
    }

    /**
     * Hiển thị danh sách suất chiếu cho phim đã chọn
     */
    public function selectSuat($phim_id)
    {
        $phim = Phim::findOrFail($phim_id);

        // Lấy suất chiếu trong tương lai, nhóm theo ngày
        $suatChieus = SuatChieu::with(['phong.rap'])
            ->where('phim_id', $phim_id)
            ->where('trang_thai', 'hoat_dong')
            ->where('gio_bat_dau', '>', Carbon::now())
            ->orderBy('gio_bat_dau')
            ->get()
            ->groupBy(function($suatChieu) {
                return Carbon::parse($suatChieu->gio_bat_dau)->format('Y-m-d');
            });

        return view('staff.donve.select_suat', compact('phim', 'suatChieus'));
    }

    /**
     * Hiển thị sơ đồ ghế để chọn ghế
     */
    public function selectSeats($suat_chieu_id)
    {
        $suatChieu = SuatChieu::with(['phim', 'phong.rap'])->findOrFail($suat_chieu_id);

        // Kiểm tra suất chiếu có hoạt động không
        if ($suatChieu->trang_thai !== 'hoat_dong') {
            return redirect()->back()->with('error', 'Suất chiếu này không khả dụng.');
        }

        // Kiểm tra thời gian chiếu
        $now = Carbon::now();
        if ($now->gte($suatChieu->gio_bat_dau)) {
            return redirect()->back()->with('error', 'Suất chiếu đã bắt đầu hoặc đã kết thúc.');
        }

        // Lấy sơ đồ ghế
        $ghes = $suatChieu->phong->ghes()
            ->orderBy('hang')
            ->orderBy('cot')
            ->get()
            ->groupBy('hang');

        // Lấy trạng thái ghế theo suất chiếu
        $gheStatuses = GheSuatChieu::where('suat_chieu_id', $suat_chieu_id)
            ->pluck('trang_thai', 'ghe_id')
            ->toArray();

        // Lấy ghế đã đặt hoặc đã thanh toán hoặc đã check-in
        $gheDaDat = ChiTietVe::where('suat_chieu_id', $suat_chieu_id)
            ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin'])
            ->pluck('ghe_id')
            ->toArray();

        // Lấy ghế giữ tạm (trong 10 phút)
        $giuTamIds = DB::table('ghe_giu_tam')
            ->where('suat_chieu_id', $suat_chieu_id)
            ->where('het_han', '>', Carbon::now())
            ->pluck('ghe_id')
            ->toArray();

        // Lấy combo và sản phẩm
        $combos = Combo::where('so_luong', '>', 0)->get();

        return view('staff.donve.select_seats', compact(
            'suatChieu',
            'ghes',
            'gheStatuses',
            'gheDaDat',
            'giuTamIds',
            'combos'
        ));
    }

    /**
     * Xử lý đặt vé tại quầy (thanh toán tiền mặt)
     */
    public function store(Request $request)
    {
        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'required|array|min:1|max:8',
            'ghe_ids.*' => 'exists:ghe,id',
            'combo_items' => 'nullable|array',
            'combo_items.*.combo_id' => 'exists:combo,id',
            'combo_items.*.so_luong' => 'integer|min:1',
        ]);

        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids;
        $comboItems = $request->combo_items ?? [];

        DB::beginTransaction();
        try {
            $suatChieu = SuatChieu::findOrFail($suatChieuId);

            // Kiểm tra thời gian
            $now = Carbon::now();
            $thoiGianBatDau = Carbon::parse($suatChieu->gio_bat_dau);
            $thoiGianToiThieu = $thoiGianBatDau->copy()->subMinutes(10);

            if ($now->gte($thoiGianBatDau)) {
                throw new \Exception('Suất chiếu đã bắt đầu.');
            }

            if ($now->gte($thoiGianToiThieu)) {
                throw new \Exception('Không thể đặt vé trong vòng 10 phút trước khi chiếu.');
            }

            // Kiểm tra ghế
            foreach ($gheIds as $gheId) {
                // Kiểm tra ghế đã đặt
                $daDat = ChiTietVe::where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin'])
                    ->exists();

                if ($daDat) {
                    $ghe = Ghe::find($gheId);
                    throw new \Exception('Ghế ' . $ghe->so_ghe_ngoi . ' đã được đặt.');
                }

                // Kiểm tra ghế giữ tạm
                $giuTam = DB::table('ghe_giu_tam')
                    ->where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->where('het_han', '>', Carbon::now())
                    ->exists();

                if ($giuTam) {
                    $ghe = Ghe::find($gheId);
                    throw new \Exception('Ghế ' . $ghe->so_ghe_ngoi . ' đang được giữ tạm.');
                }

                // Kiểm tra trạng thái ghế theo suất chiếu
                $gheStatus = GheSuatChieu::where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->value('trang_thai');

                if ($gheStatus === 'bao_tri' || $gheStatus === 'vo_hieu_hoa') {
                    $ghe = Ghe::find($gheId);
                    throw new \Exception('Ghế ' . $ghe->so_ghe_ngoi . ' không khả dụng.');
                }
            }

            // Kiểm tra ghế liên tiếp
            if (!$this->areSeatsConsecutive($gheIds)) {
                throw new \Exception('Ghế phải được chọn liên tiếp nhau trong cùng một hàng.');
            }

            // Tính tổng tiền vé theo loại ghế, ngày và khung giờ
            $tongTienVe = 0;
            $ghePrices = [];
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                $price = $this->calculateSeatPrice($suatChieu, $ghe);
                $ghePrices[$gheId] = $price;
                $tongTienVe += $price;
            }

            $tongTienCombo = 0;
            $donDatVeCombos = [];
            foreach ($comboItems as $item) {
                $combo = Combo::find($item['combo_id']);
                $soLuong = $item['so_luong'];
                $tongTienCombo += $combo->gia * $soLuong;

                $donDatVeCombos[] = [
                    'combo_id' => $combo->id,
                    'so_luong' => $soLuong,
                    'gia' => $combo->gia,
                ];
            }

            $tongTien = $tongTienVe + $tongTienCombo;

            // Tạo đơn đặt vé với trạng thái đã thanh toán (thanh toán tại quầy)
            $donDatVe = DonDatVe::create([
                'ma_don' => 'DV' . time() . rand(100, 999),
                'nguoi_dung_id' => null, // Không có user đăng nhập cho đặt vé tại quầy
                'suat_chieu_id' => $suatChieuId,
                'ma_giam_gia_id' => null, // Không áp dụng mã giảm giá cho đặt vé tại quầy
                'tong_tien' => $tongTien,
                'trang_thai' => 'da_thanh_toan', // Đã thanh toán ngay
                'thoi_gian_thanh_toan' => Carbon::now(),
                'phuong_thuc_thanh_toan' => $request->payment_method, // Phương thức thanh toán từ request
            ]);

            // Tạo chi tiết vé với trạng thái đã thanh toán
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                ChiTietVe::create([
                    'don_dat_ve_id' => $donDatVe->id,
                    'suat_chieu_id' => $suatChieuId,
                    'ghe_id' => $gheId,
                    'gia' => $ghePrices[$gheId],
                    'loai_ghe' => $ghe->loai,
                    'trang_thai' => 'da_thanh_toan',
                ]);
            }

            // Tạo chi tiết combo nếu có
            if (!empty($donDatVeCombos)) {
                foreach ($donDatVeCombos as $comboData) {
                    $combo = Combo::find($comboData['combo_id']);
                    if ($combo->so_luong < $comboData['so_luong']) {
                        throw new \Exception("Combo '{$combo->ten}' không đủ số lượng (cần {$comboData['so_luong']}, còn {$combo->so_luong}).");
                    }
                    DB::table('don_dat_ve_combo')->insert([
                        'don_dat_ve_id' => $donDatVe->id,
                        'combo_id' => $comboData['combo_id'],
                        'so_luong' => $comboData['so_luong'],
                        'gia' => $comboData['gia'],
                    ]);
                    // Decrement combo quantity
                    $combo->decrement('so_luong', $comboData['so_luong']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt vé thành công!',
                'don_dat_ve_id' => $donDatVe->id,
                'redirect' => route('staff.donve.confirm', $donDatVe->id),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Trang xác nhận đặt vé thành công
     */
    public function confirm($id)
    {
        $donDatVe = DonDatVe::with([
            'suatChieu.phim',
            'suatChieu.phong.rap',
            'chiTietVes.ghe'
        ])->findOrFail($id);

        // Lấy combo đã đặt
        $combos = DB::table('don_dat_ve_combo as ddvc')
            ->join('combo', 'combo.id', '=', 'ddvc.combo_id')
            ->where('ddvc.don_dat_ve_id', $id)
            ->select('combo.ten', 'ddvc.so_luong', 'ddvc.gia')
            ->get();

        return view('staff.donve.confirm', compact('donDatVe', 'combos'));
    }

    /**
     * Tính giá ghế dựa trên loại ghế, ngày và khung giờ
     */
    private function calculateSeatPrice($suatChieu, $ghe)
    {
        $basePrice = $suatChieu->gia_ve;

        // Tăng giá cuối tuần (thứ 7, Chủ nhật): +20%
        if ($suatChieu->gio_bat_dau->isWeekend()) {
            $basePrice *= 1.2;
        }

        // Tăng giá buổi tối từ 18h trở đi: +15%
        if ($suatChieu->gio_bat_dau->hour >= 18) {
            $basePrice *= 1.15;
        }

        // Áp dụng loại ghế
        if ($ghe->loai === 'vip') {
            $basePrice *= 1.5;
        } elseif ($ghe->loai === 'doi') {
            $basePrice *= 2;
        }

        return $basePrice;
    }

    /**
     * Kiểm tra ghế có liên tiếp không
     */
    private function areSeatsConsecutive($gheIds)
    {
        if (count($gheIds) <= 1) {
            return true;
        }

        $ghes = Ghe::whereIn('id', $gheIds)->get()->keyBy('id');

        // Nhóm ghế theo hàng
        $seatsByRow = [];
        foreach ($ghes as $ghe) {
            $seatsByRow[$ghe->hang][] = $ghe->cot;
        }

        // Nếu có nhiều hơn 1 hàng thì không liên tiếp
        if (count($seatsByRow) > 1) {
            return false;
        }

        // Lấy danh sách cột trong hàng duy nhất
        $columns = array_values($seatsByRow)[0];
        sort($columns);

        // Kiểm tra cột có liên tiếp không
        for ($i = 0; $i < count($columns) - 1; $i++) {
            if ($columns[$i + 1] - $columns[$i] !== 1) {
                return false;
            }
        }

        return true;
    }
}
