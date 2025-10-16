<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\LichSuDiem;
use App\Models\Voucher;
use App\Models\VoucherNguoiDung;

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
     * Hiển thị trang đổi điểm lấy voucher
     */
    public function rewards()
    {
        $user = Auth::user();
        
        // Lấy danh sách voucher đang kích hoạt và còn hiệu lực
        $vouchers = Voucher::where('kich_hoat', true)
            ->conHieuLuc()
            ->orderBy('diem_can', 'asc')
            ->get();

        return view('account.rewards', compact('user', 'vouchers'));
    }

    /**
     * Xử lý đổi điểm lấy voucher
     */
    public function redeemVoucher(Request $request, $voucherId)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $voucher = Voucher::findOrFail($voucherId);
            
            // Kiểm tra voucher còn hiệu lực
            if (!$voucher->conHieuLuc() || !$voucher->kich_hoat) {
                return back()->with('error', 'Voucher này hiện không khả dụng!');
            }
            
            // Kiểm tra điểm
            if ($user->diem < $voucher->diem_can) {
                return back()->with('error', "Bạn cần {$voucher->diem_can} điểm để đổi voucher này. Hiện tại bạn có {$user->diem} điểm.");
            }

            // Trừ điểm
            $user->truDiem($voucher->diem_can, "Đổi voucher: {$voucher->ten}");

            // Tạo bản ghi voucher_nguoi_dung
            $ngayHan = $voucher->ngay_ket_thuc ? $voucher->ngay_ket_thuc->endOfDay() : now()->addYear();

            VoucherNguoiDung::create([
                'nguoi_dung_id' => $user->id,
                'voucher_id' => $voucher->id,
                'diem_da_doi' => $voucher->diem_can,
                'ngay_doi' => now(),
                'ngay_han' => $ngayHan,
                'trang_thai' => 'chua_su_dung'
            ]);

            DB::commit();

            return redirect()->route('account.my-vouchers')
                ->with('success', "Đổi điểm thành công! Bạn đã nhận voucher: {$voucher->ten}. Vui lòng kiểm tra trong mục 'Voucher của tôi'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị voucher của người dùng
     */
    public function myVouchers()
    {
        $user = Auth::user();
        
        $vouchers = VoucherNguoiDung::with('voucher')
            ->where('nguoi_dung_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('account.my-vouchers', compact('user', 'vouchers'));
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
