<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use Barryvdh\DomPDF\Facade\Pdf; // cần cài DomPDF
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('admin.donve.index', compact('donDatVes'));
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

        // Nếu đơn đã hủy thì không cho xem chi tiết
        if ($donVe->trang_thai === 'da_huy') {
            return redirect()->route('admin.donve.index')->withErrors(['error' => 'Đơn đã hủy, không thể xem chi tiết.']);
        }

        return view('admin.donve.show', compact('donVe'));
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
            return redirect()->route('admin.donve.index')->withErrors(['error' => 'Chỉ có đơn đã thanh toán hoặc đã check-in mới được in vé.']);
        }

        $pdf = Pdf::loadView('admin.donve.print', compact('donVe'));
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

    if ($don->trang_thai !== 'da_thanh_toan') {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Đơn chưa được thanh toán nên không thể check-in.'], 422);
        }
        return back()->withErrors(['ma_don' => 'Đơn chưa được thanh toán nên không thể check-in.']);
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
     * Hiển thị trang check-in (form nhập mã đơn) cho admin/staff
     */
    public function showCheckinForm(Request $request)
    {
        // Có thể truyền ma_don qua query string để tự điền
        $maDon = $request->query('ma_don', '');
        return view('admin.donve.checkin', compact('maDon'));
    }
}
