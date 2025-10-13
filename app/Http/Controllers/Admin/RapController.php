<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rap;

class RapController extends Controller
{
    public function index()
    {
        $raps = Rap::all();
        return view('admin.rap_chieu.index', compact('raps'));
    }

    public function create()
    {
        return view('admin.rap_chieu.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'dia_chi' => 'required|string|max:255',
        'so_dien_thoai' => [
            'required',
            'regex:/^(0|\+84)[0-9]{9,10}$/'
        ],
        'email' => 'required|email|max:255',
    ], [
        'ten.required' => 'Tên rạp không được để trống.',
        'ten.max' => 'Tên rạp không được vượt quá 255 ký tự.',
        'dia_chi.required' => 'Địa chỉ không được để trống.',
        'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
        'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ. VD: 0901234567 hoặc +84901234567.',
        'email.required' => 'Email là bắt buộc.',
        'email.email' => 'Email không đúng định dạng.',
    ]);

    // Kiểm tra rạp trùng tên + địa chỉ
    if (Rap::where('ten', $validated['ten'])
           ->where('dia_chi', $validated['dia_chi'])
           ->exists()) {
        return back()->withInput()->withErrors([
            'ten' => 'Rạp này đã tồn tại.'
        ]);
    }

    try {
        Rap::create($validated);
        return redirect()->route('admin.rap.index')
            ->with('success', 'Thêm rạp thành công');
    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
    }
}


    public function edit($id)
    {
        $rap = Rap::findOrFail($id);
        return view('admin.rap_chieu.edit', compact('rap'));
    }

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'dia_chi' => 'required|string|max:255',
        'so_dien_thoai' => [
            'required',
            'regex:/^(0|\+84)[0-9]{9,10}$/'
        ],
        'email' => 'required|email|max:255',
    ], [
        'ten.required' => 'Tên rạp không được để trống.',
        'ten.max' => 'Tên rạp không được vượt quá 255 ký tự.',
        'dia_chi.required' => 'Địa chỉ không được để trống.',
        'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
        'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ. VD: 0901234567 hoặc +84901234567.',
        'email.required' => 'Email là bắt buộc.',
        'email.email' => 'Email không đúng định dạng.',
    ]);

    // Kiểm tra rạp trùng tên + địa chỉ, bỏ qua bản ghi hiện tại
    if (Rap::where('ten', $validated['ten'])
           ->where('dia_chi', $validated['dia_chi'])
           ->where('id', '<>', $id)
           ->exists()) {
        return back()->withInput()->withErrors([
            'ten' => 'Rạp này đã tồn tại.'
        ]);
    }

    try {
        $rap = Rap::findOrFail($id);
        $rap->update($validated);
        return redirect()->route('admin.rap.index')
            ->with('success', 'Cập nhật rạp thành công');
    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
    }
}


    public function destroy($id)
    {
        try {
            $rap = Rap::findOrFail($id);
            $rap->delete();
            return redirect()->route('admin.rap.index')
                ->with('success', 'Xóa rạp thành công');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
