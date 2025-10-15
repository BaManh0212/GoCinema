<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\LichSuDiem;
use App\Models\Combo;

class AccountController extends Controller
{
    /**
     * Hiển thị trang quản lý tài khoản
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy lịch sử điểm gần đây (10 giao dịch)
        $lichSuDiem = LichSuDiem::where('nguoi_dung_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('account.index', compact('user', 'lichSuDiem'));
    }

    /**
     * Hiển thị trang đổi điểm
     */
    public function rewards()
    {
        $user = Auth::user();
        
        // Lấy danh sách combo có thể đổi
        $combos = Combo::with('chiTiet.sanPham')
            ->orderBy('gia', 'asc')
            ->get();

        return view('account.rewards', compact('user', 'combos'));
    }

    /**
     * Xử lý đổi điểm lấy combo
     */
    public function redeemCombo(Request $request, $comboId)
    {
        try {
            $user = Auth::user();
            $combo = Combo::findOrFail($comboId);
            
            // Quy đổi: 1000 đ = 1 điểm
            $diemCanThiet = ceil($combo->gia / 1000);
            
            // Kiểm tra điểm
            if ($user->diem < $diemCanThiet) {
                return back()->with('error', "Bạn cần {$diemCanThiet} điểm để đổi combo này. Hiện tại bạn có {$user->diem} điểm.");
            }

            // Trừ điểm
            $user->truDiem($diemCanThiet, "Đổi điểm lấy combo: {$combo->ten}");

            return redirect()->route('account.rewards')
                ->with('success', "Đổi điểm thành công! Bạn đã nhận combo: {$combo->ten}. Vui lòng đến quầy để nhận hàng.");

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị lịch sử điểm đầy đủ
     */
    public function pointHistory()
    {
        $user = Auth::user();
        
        $lichSuDiem = LichSuDiem::where('nguoi_dung_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('account.point-history', compact('user', 'lichSuDiem'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dung,email,' . $user->id,
            'so_dien_thoai' => 'nullable|string|max:15',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
        ]);

        $user->update([
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
        ]);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->mat_khau)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng');
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'mat_khau' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}
