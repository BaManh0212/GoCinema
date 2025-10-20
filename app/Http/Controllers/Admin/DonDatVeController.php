<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use Barryvdh\DomPDF\Facade\Pdf; // cần cài DomPDF
use Illuminate\Http\Request;

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

        $pdf = Pdf::loadView('admin.donve.print', compact('donVe'));
        return $pdf->stream("Ve_{$donVe->ma_don}.pdf");
    }
}
