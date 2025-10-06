<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rap;

class RapController extends Controller
{
    public function index() {
        $raps = Rap::all();
        return view('admin.rap_chieu.index', compact('raps'));
    }

    public function create() {
        return view('admin.rap.create');
    }

    public function store(Request $request) {
        Rap::create($request->all());
        return redirect()->route('rap.index')->with('success', 'Thêm rạp thành công!');
    }

    public function edit($id) {
        $rap = Rap::findOrFail($id);
        return view('admin.rap.edit', compact('rap'));
    }

    public function update(Request $request, $id) {
        $rap = Rap::findOrFail($id);
        $rap->update($request->all());
        return redirect()->route('rap.index')->with('success', 'Cập nhật rạp thành công!');
    }

    public function delete($id) {
        $rap = Rap::findOrFail($id);
        $rap->delete();
        return redirect()->route('rap.index')->with('success', 'Xóa rạp thành công!');
    }
}
