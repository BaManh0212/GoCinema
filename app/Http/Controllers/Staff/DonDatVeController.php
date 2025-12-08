<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use App\Models\Phim;
use App\Models\SuatChieu;
use App\Models\Ghe;
use App\Models\ChiTietVe;

use App\Models\Combo;
use Barryvdh\DomPDF\Facade\Pdf; // cần cài DomPDF
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

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

    $allowedToPrint = ['da_thanh_toan', 'da_checkin'];
    if (!in_array($donVe->trang_thai, $allowedToPrint)) {
        return redirect()->route('staff.donve.index')
            ->withErrors(['error' => 'Chỉ có đơn đã thanh toán hoặc đã check-in mới được in vé.']);
    }

    // Check if screening time has passed and order is not yet checked in
    $now = Carbon::now();
    $thoiGianBatDau = Carbon::parse($donVe->suatChieu->gio_bat_dau);
    
    if ($now->gte($thoiGianBatDau) && $donVe->trang_thai !== 'da_checkin') {
        return redirect()->route('staff.donve.index')
            ->withErrors(['error' => 'Không thể in vé sau khi suất chiếu bắt đầu nếu chưa check-in.']);
    }

    // Log print action using CheckinPrintLog within try-catch
    try {
        \App\Models\CheckinPrintLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'don_dat_ve_id' => $donVe->id,
            'action_type' => 'print',
        ]);
    } catch (\Throwable $e) {
        \Log::error('Failed to log print action in DonDatVeController: ' . $e->getMessage());
    }

    // Update order status to 'da_checkin' if currently 'da_thanh_toan'
    if ($donVe->trang_thai === 'da_thanh_toan') {
        DB::beginTransaction();
        try {
            // Update order status
            $donVe->trang_thai = 'da_checkin';
            $donVe->save();

            // Update all seat details status to 'da_su_dung'
            foreach ($donVe->chiTietVes as $ct) {
                if ($ct->trang_thai !== 'da_su_dung') {
                    $ct->trang_thai = 'da_su_dung';
                    $ct->save();
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to update status when printing: ' . $e->getMessage());
        }
    }

    // Tạo writer dùng SVG backend (dùng payload chuẩn từ model để đồng bộ QR)
    $renderer = new ImageRenderer(
        // new RendererStyle(200, 1), // size=200, margin=1
        // new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);

    // Dùng qrString() từ model để đồng bộ với email và web confirm
    $qrString = $donVe->qrString();

    $qrCodes = [];
    foreach ($donVe->chiTietVes as $ct) {
        $qrSvg = $writer->writeString($qrString);
        $qrCodes[$ct->id] = $qrSvg;
    }

    $pdf = Pdf::loadView('staff.donve.print', compact('donVe', 'qrCodes'));
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

                // Tích điểm cho người dùng: 1 điểm cho mỗi 1000 VND
                $diemTichLuy = floor($donVe->tong_tien / 1000);
                if ($diemTichLuy > 0) {
                    $user = $donVe->nguoiDung;
                    $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donVe->ma_don);
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

    // Check if already checked in
    if ($don->trang_thai === 'da_checkin') {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Đơn này đã được check-in rồi.'], 422);
        }
        return back()->withErrors(['ma_don' => 'Đơn này đã được check-in rồi.']);
    }

    // Kiểm tra trạng thái thanh toán - chỉ 'da_thanh_toan' mới được check-in
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

        // Log checkin action using CheckinPrintLog within try-catch
        try {
            \App\Models\CheckinPrintLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'don_dat_ve_id' => $don->id,
                'action_type' => 'checkin',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to log checkin action in DonDatVeController: ' . $e->getMessage());
        }

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

    // Lấy trạng thái ghế theo phòng chiếu (bảo trì là thuộc tính của phòng)
    $gheStatuses = Ghe::where('phong_id', $suatChieu->phong_id)
        ->pluck('trang_thai', 'id')
        ->toArray();

    // Lấy ghế đã đặt hoặc đã thanh toán hoặc đã check-in
    $gheDaDat = ChiTietVe::where('suat_chieu_id', $suat_chieu_id)
        ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin'])
        ->pluck('ghe_id')
        ->toArray();

    // Lấy ghế giữ tạm (trong 10 phút) từ bảng ghe_giu_tam
    $giuTamFromTable = DB::table('ghe_giu_tam')
        ->where('suat_chieu_id', $suat_chieu_id)
        ->where('het_han', '>', Carbon::now())
        ->pluck('ghe_id')
        ->toArray();

    // Lấy ghế đang chờ thanh toán trong chi tiết vé (client holds)
    $giuTamFromClientOrders = ChiTietVe::where('suat_chieu_id', $suat_chieu_id)
        ->where('trang_thai', 'cho_thanh_toan')
        ->pluck('ghe_id')
        ->toArray();

    // Kết hợp ghế giữ tạm từ bảng và đơn chờ thanh toán
    $giuTamIds = array_unique(array_merge($giuTamFromTable, $giuTamFromClientOrders));

    // Tính giá cho từng ghế
    foreach ($ghes as $hang => $danhSachGhe) {
        foreach ($danhSachGhe as $ghe) {
            $ghe->gia = $this->calculateSeatPrice($suatChieu, $ghe);
        }
    }

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

                // Kiểm tra trạng thái ghế (bảo trì là thuộc tính của phòng)
                $ghe = Ghe::find($gheId);
                if ($ghe->trang_thai === 'bao_tri') {
                    throw new \Exception('Ghế ' . $ghe->so_ghe_ngoi . ' đang bảo trì.');
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

            // Always create booking with 'cho_thanh_toan' status
            // Payment method will be selected on the payment page
            $donDatVe = DonDatVe::create([
                'ma_don' => 'DV' . time() . rand(100, 999),
                'nguoi_dung_id' => Auth::id() ?? 1, // Use logged-in user ID or fallback to 1 (system user)
                'suat_chieu_id' => $suatChieuId,
                'ma_giam_gia_id' => null, // Không áp dụng mã giảm giá cho đặt vé tại quầy
                'tong_tien' => $tongTien,
                'trang_thai' => 'cho_thanh_toan', // Always pending payment initially
                'thoi_gian_thanh_toan' => null,
                'phuong_thuc_thanh_toan' => null, // Will be set on payment page
            ]);

            // Tạo chi tiết vé with 'cho_thanh_toan' status
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                ChiTietVe::create([
                    'don_dat_ve_id' => $donDatVe->id,
                    'suat_chieu_id' => $suatChieuId,
                    'ghe_id' => $gheId,
                    'gia' => $ghePrices[$gheId],
                    'loai_ghe' => $ghe->loai,
                    'trang_thai' => 'cho_thanh_toan',
                ]);
            }

            // Tạo chi tiết combo nếu có (không trừ kho ngay, sẽ trừ khi thanh toán)
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
                    // Do NOT decrement combo quantity here - will be done on payment
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt vé thành công!',
                'don_dat_ve_id' => $donDatVe->id,
                'redirect' => route('staff.donve.payment', $donDatVe->id),
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
            $seatsByRow[$ghe->hang][] = $ghe;
        }

        // Nếu có nhiều hơn 1 hàng thì không liên tiếp
        if (count($seatsByRow) > 1) {
            return false;
        }

        // Lấy danh sách ghế trong hàng duy nhất
        $seatsInRow = array_values($seatsByRow)[0];

        // Tính vị trí rendered cho từng ghế
        $renderedPositions = [];
        foreach ($seatsInRow as $ghe) {
            if ($ghe->loai === 'doi') {
                // Double seats are rendered at position (column + 1) / 2
                $renderedPositions[] = ($ghe->cot + 1) / 2;
            } else {
                // Regular seats are rendered at their column position
                $renderedPositions[] = $ghe->cot;
            }
        }

        // Sắp xếp vị trí rendered
        sort($renderedPositions);

        // Kiểm tra vị trí rendered có liên tiếp không
        for ($i = 0; $i < count($renderedPositions) - 1; $i++) {
            if ($renderedPositions[$i + 1] - $renderedPositions[$i] !== 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Hiển thị trang chọn phương thức thanh toán (Momo)
     */
    public function payment($id)
    {
        $donDatVe = DonDatVe::with(['suatChieu.phim', 'suatChieu.phong.rap', 'chiTietVes'])
            ->findOrFail($id);

        if ($donDatVe->trang_thai !== 'cho_thanh_toan') {
            return redirect()->route('staff.donve.show', $id)
                ->with('info', 'Đơn này đã được thanh toán rồi.');
        }

        // Lấy combo đã đặt
        $combos = DB::table('don_dat_ve_combo as ddvc')
            ->join('combo', 'combo.id', '=', 'ddvc.combo_id')
            ->where('ddvc.don_dat_ve_id', $id)
            ->select('combo.ten', 'ddvc.so_luong', 'ddvc.gia')
            ->get();

        return view('staff.donve.payment', compact('donDatVe', 'combos'));
    }

    /**
     * Xử lý yêu cầu thanh toán (Cash hoặc MoMo)
     */
    public function processPayment(Request $request, $id)
    {
        Log::info('Staff processPayment request', ['id' => $id, 'payment_method' => $request->payment_method]);
        
        $request->validate([
            'payment_method' => 'required|in:cash,momo',
        ]);

        $donDatVe = DonDatVe::findOrFail($id);

        if (!in_array($donDatVe->trang_thai, ['cho_thanh_toan', 'da_thanh_toan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn này không thể thanh toán.',
            ], 400);
        }

        $paymentMethod = $request->payment_method;

        //===== Xử lý thanh toán tiền mặt ======//
        if ($paymentMethod === 'cash') {
            DB::beginTransaction();
            try {
                // Update booking status to paid
                $donDatVe->update([
                    'trang_thai' => 'da_thanh_toan',
                    'thoi_gian_thanh_toan' => Carbon::now(),
                    'phuong_thuc_thanh_toan' => 'cash',
                ]);

                // Update ticket details status
                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                // Decrement combo inventory
                $donDatVe->load('combos');
                foreach ($donDatVe->combos as $combo) {
                    $soLuongMua = (int) ($combo->pivot->so_luong ?? 0);
                    if ($soLuongMua <= 0) continue;
                    if ($combo->so_luong < $soLuongMua) {
                        throw new \Exception("Combo '{$combo->ten}' không đủ số lượng (cần {$soLuongMua}, còn {$combo->so_luong}).");
                    }
                    $combo->so_luong = $combo->so_luong - $soLuongMua;
                    $combo->save();
                }

                // Process loyalty points: 1 point per 1000 VND
                $diemTichLuy = floor($donDatVe->tong_tien / 1000);
                if ($diemTichLuy > 0) {
                    $user = $donDatVe->nguoiDung;
                    $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don);
                }

                DB::commit();

                Log::info('Cash payment successful (Staff)', ['order_id' => $donDatVe->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán tiền mặt thành công!',
                    'redirect' => route('staff.donve.show', $donDatVe->id),
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error processing cash payment (Staff): ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage(),
                ], 500);
            }
        }

        //===== Xử lý thanh toán MoMo ======//
        if ($paymentMethod === 'momo') {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

            $partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
            $accessKey = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
            $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');

            $orderId = 'STAFF_' . $donDatVe->id . '_' . time();
            $amount = (int) round($donDatVe->tong_tien);
            $orderInfo = 'Thanh toán đơn ' . $donDatVe->ma_don;
            $redirectUrl = route('staff.donve.momo-return');
            $ipnUrl = route('staff.donve.momo-callback');
            $requestId = (string) time();
            $requestType = 'payWithATM';
            $extraData = '';

            $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
            $signature = hash_hmac('sha256', $rawHash, $secretKey);

            $data = [
                'partnerCode' => $partnerCode,
                'accessKey'   => $accessKey,
                'requestId'   => $requestId,
                'amount'      => (string)$amount,
                'orderId'     => $orderId,
                'orderInfo'   => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl'      => $ipnUrl,
                'lang'        => 'vi',
                'extraData'   => $extraData,
                'requestType' => $requestType,
                'signature'   => $signature,
            ];

            Log::info('MoMo request (Staff)', ['orderId' => $orderId, 'amount' => $amount]);

            try {
                $response = Http::timeout(10)->post($endpoint, $data)->json();
                Log::info('MoMo response (Staff)', $response);
            } catch (\Exception $e) {
                Log::error('MoMo request error (Staff): ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi kết nối MoMo: ' . $e->getMessage(),
                ], 500);
            }

            if (!empty($response) && isset($response['resultCode']) && $response['resultCode'] == 0 && !empty($response['payUrl'])) {
                // Lưu phương thức thanh toán TRƯỚC khi redirect
                DB::beginTransaction();
                try {
                    $donDatVe->update([
                        'phuong_thuc_thanh_toan' => 'momo',
                    ]);
                    DB::commit();
                    
                    Log::info('MoMo payment initiated (Staff)', ['order_id' => $donDatVe->id, 'orderId' => $orderId]);
                    
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => true, 'payUrl' => $response['payUrl']]);
                    }
                    return redirect()->away($response['payUrl']);
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error updating MoMo phuong_thuc (Staff): ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Lỗi cập nhật phương thức thanh toán'], 500);
                }
            }

            Log::warning('MoMo create failed (Staff)', $response ?? []);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo thanh toán MoMo: ' . ($response['message'] ?? 'Không xác định'),
            ], 500);
        }
    }

    /**
     * Xử lý callback từ MoMo IPN
     */
    public function momoCallback(Request $request)
    {
        Log::info('MoMo IPN received (Staff)', $request->all());

        $orderId = $request->input('orderId');
        $resultCode = $request->input('resultCode');

        if (!$orderId) {
            Log::warning('MoMo IPN missing orderId (Staff)', $request->all());
            return response('Missing orderId', 400);
        }

        // Extract ID từ orderId (STAFF_23_timestamp)
        $parts = explode('_', $orderId);
        if (count($parts) < 2 || $parts[0] !== 'STAFF') {
            Log::warning('MoMo IPN invalid orderId format (Staff): ' . $orderId);
            return response('Invalid orderId', 400);
        }
        
        $id = (int) $parts[1];
        $donDatVe = DonDatVe::find($id);
        
        if (!$donDatVe) {
            Log::warning('MoMo IPN order not found (Staff): ' . $orderId);
            return response('Order not found', 404);
        }

        DB::beginTransaction();
        try {
            // resultCode = 0: thành công
            if ((string)$resultCode === '0') {
                Log::info('MoMo IPN payment successful (Staff)', ['orderId' => $orderId]);
                
                // Chỉ cập nhật nếu chưa thanh toán
                if ($donDatVe->trang_thai !== 'da_thanh_toan') {
                    $donDatVe->update([
                        'trang_thai' => 'da_thanh_toan',
                        'thoi_gian_thanh_toan' => Carbon::now(),
                        'phuong_thuc_thanh_toan' => 'momo',
                    ]);
                    $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                    // Tích điểm cho người dùng: 1 điểm cho mỗi 1000 VND
                    $diemTichLuy = floor($donDatVe->tong_tien / 1000);
                    if ($diemTichLuy > 0) {
                        $user = $donDatVe->nguoiDung;
                        $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don);
                    }

                    Log::info('Order marked paid via MoMo callback (Staff)', ['orderId' => $orderId, 'don_dat_ve_id' => $id]);
                } else {
                    Log::info('Order already marked paid, skipping update (Staff)', ['orderId' => $orderId]);
                }
                
                DB::commit();
                return response('OK', 200);
            }

            // resultCode !== 0: thanh toán thất bại
            Log::warning('MoMo IPN returned failure (Staff)', ['orderId' => $orderId, 'resultCode' => $resultCode]);
            DB::commit();
            return response('Ignored', 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing MoMo callback (Staff): ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return response('Internal error', 500);
        }
    }

    /**
     * Xử lý redirect từ MoMo sau khi thanh toán
     */
    public function momoReturn(Request $request)
    {
        Log::info('MoMo return (GET redirect) (Staff)', $request->all());
        
        $orderId = $request->query('orderId');
        $resultCode = $request->query('resultCode');

        if (!$orderId) {
            return redirect()->route('staff.donve.index')->with('error', 'Thiếu dữ liệu trả về từ MoMo.');
        }

        // Extract ID từ orderId
        $parts = explode('_', $orderId);
        if (count($parts) < 2 || $parts[0] !== 'STAFF') {
            return redirect()->route('staff.donve.index')->with('error', 'Dữ liệu orderId không hợp lệ.');
        }

        $id = (int) $parts[1];
        $donDatVe = DonDatVe::find($id);

        if (!$donDatVe) {
            return redirect()->route('staff.donve.index')->with('error', 'Đơn đặt vé không tồn tại.');
        }

        // ✅ FIX: resultCode = 0 => thanh toán thành công => CẬP NHẬT TRẠNG THÁI NGAY
        if ((string)$resultCode === '0') {
            Log::info('MoMo payment successful, updating status (Staff)', ['id' => $id, 'orderId' => $orderId]);
            
            DB::beginTransaction();
            try {
                // ✅ Cập nhật trạng thái ngay
                $donDatVe->update([
                    'trang_thai' => 'da_thanh_toan',
                    'thoi_gian_thanh_toan' => Carbon::now(),
                    'phuong_thuc_thanh_toan' => 'momo',
                ]);
                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                // Tích điểm cho người dùng: 1 điểm cho mỗi 1000 VND
                $diemTichLuy = floor($donDatVe->tong_tien / 1000);
                if ($diemTichLuy > 0) {
                    $user = $donDatVe->nguoiDung;
                    $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don);
                }

                // Xóa ghế giữ tạm của người dùng cho suất chiếu này
                DB::table('ghe_giu_tam')
                    ->where('nguoi_dung_id', $donDatVe->nguoi_dung_id)
                    ->where('suat_chieu_id', $donDatVe->suat_chieu_id)
                    ->delete();
                
                DB::commit();
                return redirect()->route('staff.donve.show', $id)->with('success', 'Thanh toán MoMo thành công!');
                
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error updating status in momoReturn (Staff): ' . $e->getMessage());
                return redirect()->route('staff.donve.payment', $id)->with('error', 'Lỗi cập nhật trạng thái: ' . $e->getMessage());
            }
        }

        // resultCode !== 0: thanh toán thất bại
        Log::warning('MoMo payment failed (Staff)', ['orderId' => $orderId, 'resultCode' => $resultCode]);
        return redirect()->route('staff.donve.payment', $id)
            ->with('error', 'Thanh toán MoMo không thành công. Vui lòng thử lại hoặc chọn phương thức khác.');
    }
}
