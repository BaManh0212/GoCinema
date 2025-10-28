<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Hiển thị danh sách voucher đổi điểm
     */
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('ten', 'like', '%' . $request->search . '%');
        }

        // Lọc theo loại
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        // Lọc theo áp dụng cho
        if ($request->filled('ap_dung_cho')) {
            $query->where('ap_dung_cho', $request->ap_dung_cho);
        }

        // Lọc theo trạng thái kích hoạt
        if ($request->filled('kich_hoat')) {
            $query->where('kich_hoat', $request->kich_hoat == '1');
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $vouchers = $query->paginate(10)->withQueryString();

        return view('admin.voucher.index', compact('vouchers'));
    }

    /**
     * Hiển thị form tạo voucher mới
     */
    public function create()
    {
        return view('admin.voucher.create');
    }

    /**
     * Lưu voucher mới
     */
    public function store(Request $request)
{
    // ✅ Chuyển checkbox sang true/false để validate đúng kiểu boolean
    $request->merge([
        'kich_hoat' => $request->has('kich_hoat')
    ]);

    // ✅ Để Laravel tự xử lý lỗi validate
    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'loai' => 'required|in:phan_tram,so_tien',
        'gia_tri' => 'required|numeric|min:0',
        'gia_tri_don_hang_toi_thieu' => 'nullable|numeric|min:0',
        'ap_dung_cho' => 'required|in:ve,san_pham,tat_ca',
        'so_lan_su_dung' => 'required|integer|min:1',
        'diem_can' => 'required|integer|min:1',
        'ngay_bat_dau' => 'nullable|date',
        'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
        'kich_hoat' => 'boolean'
    ], [
        'ten.required' => 'Vui lòng nhập tên voucher',
        'loai.required' => 'Vui lòng chọn loại voucher',
        'gia_tri.required' => 'Vui lòng nhập giá trị voucher',
        'gia_tri.min' => 'Giá trị voucher phải lớn hơn 0',
        'ap_dung_cho.required' => 'Vui lòng chọn loại áp dụng',
        'so_lan_su_dung.required' => 'Vui lòng nhập số lần sử dụng',
        'so_lan_su_dung.min' => 'Số lần sử dụng phải lớn hơn 0',
        'diem_can.required' => 'Vui lòng nhập số điểm cần để đổi',
        'diem_can.min' => 'Số điểm phải lớn hơn 0',
        'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu'
    ]);

    try {
        // ✅ Gán giá trị mặc định cho các cột hệ thống
        $validated['so_luong_da_dung'] = 0;
        $validated['so_luong_toi_da'] = $request->input('so_luong_toi_da', 0);

        \App\Models\Voucher::create($validated);

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Tạo voucher đổi điểm thành công!');
    } catch (\Throwable $e) {
        return back()->with('error', 'Lỗi khi lưu: ' . $e->getMessage());
    }
}

    /**
     * Hiển thị chi tiết voucher
     */
    public function show($id)
    {
        $voucher = Voucher::with(['nguoiDungDaDoi' => function($query) {
            $query->orderBy('voucher_nguoi_dung.created_at', 'desc')->limit(10);
        }])->findOrFail($id);

        // Thống kê
        $stats = DB::table('voucher_nguoi_dung')
            ->where('voucher_id', $id)
            ->selectRaw('
                COUNT(*) as so_luot_doi,
                SUM(diem_da_doi) as tong_diem_da_doi,
                COUNT(DISTINCT nguoi_dung_id) as so_nguoi_da_doi
            ')
            ->first();

        return view('admin.voucher.show', compact('voucher', 'stats'));
    }

    /**
     * Hiển thị form sửa voucher
     */
    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.voucher.edit', compact('voucher'));
    }

    /**
     * Cập nhật voucher
     */
    public function update(Request $request, $id)
{
    $voucher = Voucher::findOrFail($id);

    // ✅ Chuyển checkbox sang true/false TRƯỚC KHI validate
    $request->merge([
        'kich_hoat' => $request->has('kich_hoat')
    ]);

    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'loai' => 'required|in:phan_tram,so_tien',
        'gia_tri' => 'required|numeric|min:0',
        'gia_tri_don_hang_toi_thieu' => 'nullable|numeric|min:0',
        'ap_dung_cho' => 'required|in:ve,san_pham,tat_ca',
        'so_lan_su_dung' => 'required|integer|min:1',
        'diem_can' => 'required|integer|min:1',
        'ngay_bat_dau' => 'nullable|date',
        'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
        'kich_hoat' => 'boolean'
    ], [
        'ten.required' => 'Vui lòng nhập tên voucher',
        'loai.required' => 'Vui lòng chọn loại voucher',
        'gia_tri.required' => 'Vui lòng nhập giá trị voucher',
        'gia_tri.min' => 'Giá trị voucher phải lớn hơn 0',
        'ap_dung_cho.required' => 'Vui lòng chọn loại áp dụng',
        'so_lan_su_dung.required' => 'Vui lòng nhập số lần sử dụng',
        'so_lan_su_dung.min' => 'Số lần sử dụng phải lớn hơn 0',
        'diem_can.required' => 'Vui lòng nhập số điểm cần để đổi',
        'diem_can.min' => 'Số điểm phải lớn hơn 0',
        'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu'
    ]);

    // Không cần set lại ở đây nữa (đã merge ở trên)
    $voucher->update($validated);

    return redirect()->route('admin.voucher.index')
        ->with('success', 'Cập nhật voucher thành công!');
}


    public function trashed()
    {
        $vouchers = Voucher::onlyTrashed()->paginate(10);
        return view('admin.voucher.trashed', compact('vouchers'));
    }

    /**
     * Xóa mềm voucher
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);

        // ⚠️ Kiểm tra xem có người dùng nào đã đổi voucher này chưa
        $daDuocDoi = DB::table('voucher_nguoi_dung')
            ->where('voucher_id', $id)
            ->exists();

        if ($daDuocDoi) {
            return back()->with('error', 'Không thể xóa voucher này vì đã có người dùng đổi!');
        }

        // 🗑️ Xóa mềm
        $voucher->delete();

        return redirect()->route('admin.voucher.index')
            ->with('success', 'Voucher đã được chuyển vào thùng rác!');
    }

    /**
     * Khôi phục voucher từ thùng rác
     */
    public function restore($id)
    {
        $voucher = Voucher::onlyTrashed()->findOrFail($id);
        $voucher->restore();

        return redirect()->route('admin.voucher.trashed')
            ->with('success', 'Voucher đã được khôi phục thành công!');
    }

    /**
     * Xóa vĩnh viễn voucher
     */
    public function forceDelete($id)
    {
        $voucher = Voucher::onlyTrashed()->findOrFail($id);
        $voucher->forceDelete();

        return redirect()->route('admin.voucher.trashed')
            ->with('success', 'Voucher đã bị xóa vĩnh viễn!');
    }
    /**
     * Bật/tắt kích hoạt voucher
     */
    public function toggleStatus($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->kich_hoat = !$voucher->kich_hoat;
        $voucher->save();

        $status = $voucher->kich_hoat ? 'kích hoạt' : 'vô hiệu hóa';
        return back()->with('success', "Đã {$status} voucher thành công!");
    }

    /**
     * Thống kê voucher
     */
    public function statistics()
    {
        // Top 10 voucher được đổi nhiều nhất
        $topVouchers = Voucher::select('voucher.*')
            ->join('voucher_nguoi_dung', 'voucher.id', '=', 'voucher_nguoi_dung.voucher_id')
            ->selectRaw('COUNT(voucher_nguoi_dung.id) as so_luot_doi')
            ->selectRaw('SUM(voucher_nguoi_dung.diem_da_doi) as tong_diem')
            ->groupBy('voucher.id')
            ->orderByDesc('so_luot_doi')
            ->limit(10)
            ->get();

        // Thống kê theo tháng
        $thongKeThang = DB::table('voucher_nguoi_dung')
            ->selectRaw('MONTH(ngay_doi) as thang, COUNT(*) as so_luot, SUM(diem_da_doi) as tong_diem')
            ->whereYear('ngay_doi', date('Y'))
            ->groupBy('thang')
            ->orderBy('thang')
            ->get()
            ->keyBy('thang');

        // Tạo dữ liệu đầy đủ 12 tháng
        $thongKeTheoThang = collect(range(1, 12))->map(function($thang) use ($thongKeThang) {
            return [
                'thang' => $thang,
                'so_luot' => $thongKeThang->get($thang)->so_luot ?? 0,
                'tong_diem' => $thongKeThang->get($thang)->tong_diem ?? 0
            ];
        });

        // Tổng quan
        $tongQuan = [
            'tong_voucher' => Voucher::count(),
            'voucher_dang_hoat_dong' => Voucher::where('kich_hoat', true)->count(),
            'tong_luot_doi' => DB::table('voucher_nguoi_dung')->count(),
            'tong_diem_da_doi' => DB::table('voucher_nguoi_dung')->sum('diem_da_doi')
        ];

        return view('admin.voucher.statistics', compact('topVouchers', 'thongKeTheoThang', 'tongQuan'));
    }
}
