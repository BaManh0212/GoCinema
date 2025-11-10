<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\NguoiDung;
use App\Models\VaiTro;
use App\Models\LichSuDiem;

class NguoiDungController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     */
    public function index(Request $request)
    {
        $query = NguoiDung::with('vaiTro');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ho_ten', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'like', "%{$search}%");
            });
        }

        // Lọc theo vai trò
        if ($request->filled('vai_tro_id')) {
            $query->where('vai_tro_id', $request->vai_tro_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $nguoiDung = $query->paginate(10);
        $vaiTros = VaiTro::all();

        return view('admin.nguoi-dung.index', compact('nguoiDung', 'vaiTros'));
    }

    /**
     * Hiển thị form tạo người dùng mới
     */
    public function create()
    {
        $vaiTros = VaiTro::all();
        return view('admin.nguoi-dung.create', compact('vaiTros'));
    }

    /**
     * Lưu người dùng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dung,email',
            'mat_khau' => 'required|min:6',
            'so_dien_thoai' => 'nullable|string|max:15',
            'vai_tro_id' => 'required|exists:vai_tro,id',
            'diem_tich_luy' => 'nullable|integer|min:0',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'vai_tro_id.required' => 'Vui lòng chọn vai trò',
        ]);

        NguoiDung::create([
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->mat_khau),
            'so_dien_thoai' => $request->so_dien_thoai,
            'vai_tro_id' => $request->vai_tro_id,
            'diem_tich_luy' => $request->diem_tich_luy ?? 0,
            'trang_thai' => $request->trang_thai ?? 1,
        ]);

        return redirect()->route('admin.nguoi-dung.index')
            ->with('success', 'Tạo người dùng thành công!');
    }

    /**
     * Hiển thị chi tiết người dùng
     */
    public function show($id)
    {
        $nguoiDung = NguoiDung::with(['vaiTro', 'lichSuDiem'])->findOrFail($id);
        
        // Lấy lịch sử điểm
        $lichSuDiem = LichSuDiem::where('nguoi_dung_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Thống kê
        $tongTichLuy = LichSuDiem::where('nguoi_dung_id', $id)
            ->where('hanh_dong', 'tich_luy')
            ->sum('diem');
            
        $tongSuDung = LichSuDiem::where('nguoi_dung_id', $id)
            ->where('hanh_dong', 'su_dung')
            ->sum('diem');

        return view('admin.nguoi-dung.show', compact('nguoiDung', 'lichSuDiem', 'tongTichLuy', 'tongSuDung'));
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        $vaiTros = VaiTro::all();
        return view('admin.nguoi-dung.edit', compact('nguoiDung', 'vaiTros'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dung,email,' . $id,
            'so_dien_thoai' => 'nullable|string|max:15',
            'vai_tro_id' => 'required|exists:vai_tro,id',
            'mat_khau' => 'nullable|min:6',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email này đã được sử dụng',
            'vai_tro_id.required' => 'Vui lòng chọn vai trò',
        ]);

        $data = [
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'vai_tro_id' => $request->vai_tro_id,
            'trang_thai' => $request->trang_thai ?? 1,
        ];

        // Chỉ cập nhật mật khẩu nếu có nhập
        if ($request->filled('mat_khau')) {
            $data['mat_khau'] = Hash::make($request->mat_khau);
        }

        $nguoiDung->update($data);

        return redirect()->route('admin.nguoi-dung.index')
            ->with('success', 'Cập nhật người dùng thành công!');
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        
        // Không cho xóa tài khoản đang đăng nhập
        if ($nguoiDung->id == auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập!');
        }

        $nguoiDung->delete();

        return redirect()->route('admin.nguoi-dung.index')
            ->with('success', 'Xóa người dùng thành công!');
    }

    /**
     * Thay đổi trạng thái người dùng
     */
   public function toggleTrangThai($id)
{
    $nguoiDung = \App\Models\NguoiDung::findOrFail($id);

    // Đảo trạng thái: nếu 1 thì thành 0, nếu 0 thì thành 1
    $nguoiDung->trang_thai = !$nguoiDung->trang_thai;
    $nguoiDung->save();

    // Thông báo phù hợp
    $message = $nguoiDung->trang_thai 
        ? '✅ Đã mở khóa tài khoản thành công.'
        : '🔒 Đã khóa tài khoản thành công.';

    return redirect()->route('admin.nguoi-dung.index')->with('success', $message);
}

}
