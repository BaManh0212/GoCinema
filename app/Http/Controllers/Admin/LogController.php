<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckinPrintLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = CheckinPrintLog::with(['user', 'donDatVe'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }
}
