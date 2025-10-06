<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'totalUsers' => 0,
            'activeUsers' => 0,
            'newThisWeek' => 0,
            'conversionRate' => 0.0,
        ];

        return view('dashboard', [
            'stats' => $stats,
            'pageTitle' => 'Dashboard',
        ]);
    }
}
