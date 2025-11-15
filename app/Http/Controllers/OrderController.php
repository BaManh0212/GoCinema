<?php

namespace App\Http\Controllers;

use App\Models\DonDatVe;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = DonDatVe::where('nguoi_dung_id', auth()->id())
            ->with([
                'suatChieu.phim',
                'suatChieu.phong',
                'combos',
            ])
            ->latest()
            ->get();

        return view('client.orders.index', compact('orders'));
    }
    // Xem chi tiết đơn vé
    public function show($id)
    {
        // Lấy đơn vé theo id của user hiện tại
        $order = DonDatVe::with([
            'suatChieu.phim', 
            'suatChieu.phong',
            'combos'
        ])
        ->where('id', $id)
        ->where('nguoi_dung_id', auth()->id())
        ->firstOrFail();

        return view('client.orders.show', compact('order'));
    }
}
