<?php

namespace App\Console\Commands;

use App\Models\MonthlyReport;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly reports for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $targetMonth = Carbon::now()->subMonth()->format('Y-m'); // Misalnya "2025-06"

        foreach ($users as $user) {
            // Cek apakah sudah ada laporan bulan itu
            $exists = MonthlyReport::where('user_id', $user->id)
                ->where('month', $targetMonth)
                ->exists();

            if (!$exists) {
                // Hitung income & expense bulan sebelumnya
                $totalIncome = Transaction::where('user_id', $user->id)
                    ->whereMonth('date', Carbon::now()->subMonth()->month)
                    ->whereYear('date', Carbon::now()->subMonth()->year)
                    ->where('type', 'income')
                    ->sum('amount');

                $totalExpense = Transaction::where('user_id', $user->id)
                    ->whereMonth('date', Carbon::now()->subMonth()->month)
                    ->whereYear('date', Carbon::now()->subMonth()->year)
                    ->where('type', 'expense')
                    ->sum('amount');

                $cashBalance = $totalIncome - $totalExpense;

                MonthlyReport::create([
                    'user_id' => $user->id,
                    'month' => $targetMonth,
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'cash_balance' => $cashBalance,
                ]);

                $this->info("Report generated for user: {$user->id} - $targetMonth");
            }
        }

        $this->info('All monthly reports generated.');
    }
}
