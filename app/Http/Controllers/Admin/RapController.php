<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rap;
use Illuminate\Http\Request;

class RapController extends Controller
{
    public function index()
    {
        // Lấy rạp đầu tiên (vì chỉ có 1)
        $rap = Rap::with('phongchieus')->firstOrFail();

        return view('admin.rap_chieu.index', compact('rap'));
    }
}
