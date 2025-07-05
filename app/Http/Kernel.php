<?php

namespace App\Http;

use App\Console\Commands\GenerateMonthlyReports;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected function middleware(): array
    {
        return [
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ];
    }

    protected function middlewareGroups(): array
    {
        return [
            'web' => [
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],

            'api' => [
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
                \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
        ];
    }

    protected function routeMiddleware(): array
    {
        return [
            'auth' => \App\Http\Middleware\Authenticate::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ];
    }

    protected function schedule(Schedule $schedule)
    {
        // Menjalankan setiap tanggal 1 pukul 00:00
        $schedule->command('monthly:generate')->monthlyOn(1, '00:00');
    }

    protected $commands = [
        GenerateMonthlyReports::class,
    ];
}
