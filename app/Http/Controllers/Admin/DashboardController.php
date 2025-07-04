<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller 
{
    public function index()
    {
        $userId = Auth::id();

        $reports = MonthlyReport::where('user_id', $userId)
        ->orderBy('month')
        ->get();

        // Untuk grafik dan list
        return Inertia::render('dashboard', [
            'chartData' => $reports->map(fn ($r) => [
                'month' => $r->month,
                'income' => $r->total_income,
                'expense' => $r->total_expense,
                'balance' => $r->cash_balance,
            ]),
            'recentReports' => $reports->sortByDesc('month')->take(5)->values(),
        ]);
    }
}
