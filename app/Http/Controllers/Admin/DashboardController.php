<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data transaksi dan kelompokkan per bulan (gabung semua user)
        $reports = Transaction::select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($r) {
                return [
                    'month' => $r->month,
                    'income' => (int) $r->total_income,
                    'expense' => (int) $r->total_expense,
                    'balance' => (int) $r->total_income - (int) $r->total_expense,
                ];
            });

        return Inertia::render('dashboard', [
            'chartData' => $reports,
            'recentReports' => $reports->sortByDesc('month')->take(5)->values(),
        ]);
    }
}
