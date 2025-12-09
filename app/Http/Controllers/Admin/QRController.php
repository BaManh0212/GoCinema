<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function scanPage()
    {
        return view('admin.donve.scan_qr');
    }

    public function check(Request $request)
    {
        $maDon = trim($request->ma_don ?? '');

        if (!$maDon) {
            return response()->json([
                'status' => false,
                'message' => 'Mã đơn trống, vui lòng thử lại.'
            ]);
        }

        $don = DonDatVe::where('ma_don', $maDon)->first();

        if (!$don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn đặt vé!'
            ]);
        }

        // Nếu đã check-in rồi -> không cho quét lại
        if ($don->trang_thai === 'da_checkin') {
            return response()->json([
                'status' => false,
                'message' => 'Đơn này đã được check-in rồi. Không thể quét lại.'
            ]);
        }

        // Nếu chưa thanh toán -> không cho check-in (vé chỉ "đặt" -> không check-in được)
        if ($don->trang_thai !== 'da_thanh_toan') {
            return response()->json([
                'status' => false,
                'message' => 'Đơn này chưa được thanh toán. Vé đã đặt chưa thể check-in.'
            ]);
        }

        // Nếu đạt yêu cầu -> chuyển tới trang xem chi tiết (nơi staff có thể xác nhận check-in)
        return response()->json([
            'status' => true,
            'redirect' => route('admin.admin.orders.showQR', $don->ma_don)
        ]);
    }
}
