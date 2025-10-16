<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NguoiDung;
use App\Models\LichSuDiem;

class DiemTichLuyController extends Controller
{
    /**
     * Hiển thị trang quản lý điểm
     */
    public function index(Request $request)
    {
        $query = LichSuDiem::with('nguoiDung');

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nguoiDung', function($q) use ($search) {
                $q->where('ho_ten', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại giao dịch
        if ($request->filled('hanh_dong')) {
            $query->where('hanh_dong', $request->hanh_dong);
        }

        // Lọc theo ngày
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        $lichSuDiem = $query->orderBy('created_at', 'desc')->paginate(20);

        // Thống kê
        $tongTichLuy = LichSuDiem::where('hanh_dong', 'tich_luy')->sum('diem');
        $tongSuDung = LichSuDiem::where('hanh_dong', 'su_dung')->sum('diem');
        $tongNguoiDung = NguoiDung::count();
        $tongDiem = NguoiDung::sum('diem_tich_luy');

        return view('admin.diem-tich-luy.index', compact(
            'lichSuDiem', 
            'tongTichLuy', 
            'tongSuDung', 
            'tongNguoiDung',
            'tongDiem'
        ));
    }

    /**
     * Hiển thị form thêm điểm cho người dùng
     */
    public function create()
    {
        $nguoiDungs = NguoiDung::orderBy('ho_ten')->get();
        return view('admin.diem-tich-luy.create', compact('nguoiDungs'));
    }

    /**
     * Thêm điểm cho người dùng
     */
    public function store(Request $request)
    {
        $request->validate([
            'nguoi_dung_id' => 'required|exists:nguoi_dung,id',
            'diem' => 'required|integer|min:1',
            'hanh_dong' => 'required|in:tich_luy,su_dung',
            'mo_ta' => 'required|string|max:255',
        ], [
            'nguoi_dung_id.required' => 'Vui lòng chọn người dùng',
            'diem.required' => 'Vui lòng nhập số điểm',
            'diem.min' => 'Số điểm phải lớn hơn 0',
            'hanh_dong.required' => 'Vui lòng chọn loại giao dịch',
            'mo_ta.required' => 'Vui lòng nhập mô tả',
        ]);

        try {
            $nguoiDung = NguoiDung::findOrFail($request->nguoi_dung_id);

            if ($request->hanh_dong == 'tich_luy') {
                $nguoiDung->themDiem($request->diem, $request->mo_ta . ' (Admin)');
                $message = 'Thêm điểm thành công!';
            } else {
                $nguoiDung->truDiem($request->diem, $request->mo_ta . ' (Admin)');
                $message = 'Trừ điểm thành công!';
            }

            return redirect()->route('admin.diem-tich-luy.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xem chi tiết lịch sử điểm của 1 người dùng
     */
    public function show($nguoiDungId)
    {
        $nguoiDung = NguoiDung::with('vaiTro')->findOrFail($nguoiDungId);
        
        $lichSuDiem = LichSuDiem::where('nguoi_dung_id', $nguoiDungId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $tongTichLuy = LichSuDiem::where('nguoi_dung_id', $nguoiDungId)
            ->where('hanh_dong', 'tich_luy')
            ->sum('diem');
            
        $tongSuDung = LichSuDiem::where('nguoi_dung_id', $nguoiDungId)
            ->where('hanh_dong', 'su_dung')
            ->sum('diem');

        return view('admin.diem-tich-luy.show', compact(
            'nguoiDung',
            'lichSuDiem',
            'tongTichLuy',
            'tongSuDung'
        ));
    }

    /**
     * Xóa lịch sử điểm (chỉ admin cấp cao)
     */
    public function destroy($id)
    {
        $lichSu = LichSuDiem::findOrFail($id);
        $lichSu->delete();

        return back()->with('success', 'Xóa lịch sử thành công!');
    }

    /**
     * Thống kê điểm
     */
    public function statistics()
    {
        // Top người dùng có nhiều điểm nhất
        $topNguoiDung = NguoiDung::orderBy('diem_tich_luy', 'desc')
            ->take(10)
            ->get();

        // Thống kê theo tháng
        $thongKeThang = LichSuDiem::selectRaw('
                MONTH(created_at) as thang,
                YEAR(created_at) as nam,
                hanh_dong,
                SUM(diem) as tong_diem
            ')
            ->whereYear('created_at', date('Y'))
            ->groupBy('thang', 'nam', 'hanh_dong')
            ->orderBy('thang')
            ->get();

        return view('admin.diem-tich-luy.statistics', compact('topNguoiDung', 'thongKeThang'));
    }
}
