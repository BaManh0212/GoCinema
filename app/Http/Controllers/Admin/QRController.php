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
        $maDon = $request->ma_don;

        $don = DonDatVe::where('ma_don', $maDon)->first();

        if (!$don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn đặt vé!'
            ]);
        }

        return response()->json([
            'status' => true,
            'redirect' => route('admin.admin.orders.showQR', $don->ma_don)
        ]);
    }
}
