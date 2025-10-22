<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rap;
use Illuminate\Http\Request;

class RapController extends Controller
{
    // Nếu chỉ có 1 rạp duy nhất, ta chỉ cần show()
    public function show()
    {
        // Lấy rạp đầu tiên (vì chỉ có 1)
        $rap = Rap::with('phongchieus')->firstOrFail();

        return view('admin.rap_chieu.show', compact('rap'));
    }
}
