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
            'ten' => 'required|max:255',
            'dia_chi' => 'required',
            // 'so_dien_thoai' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            // 'email' => 'required|email',
            // 'trang_thai' => 'required|in:0,1'
        ]);

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
            'ten' => 'required|max:255',
            'dia_chi' => 'required',
        ]);

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
