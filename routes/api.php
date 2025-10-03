<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\MonthlyReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rute yang TIDAK memerlukan autentikasi (misalnya, login, register, forgot password)
Route::post('/login', [AuthController::class, 'login']); // Contoh jika Anda punya AuthController
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // Contoh
Route::post('/reset-password', [AuthController::class, 'resetPassword']); // Contoh

// Grup rute yang memerlukan autentikasi (Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    // Rute default Laravel untuk user yang terautentikasi (gunakan user() method dari AuthController jika ada)
    // Route::get('/user', function (Request $request) {
    //      return $request->user();
    // });
    // Lebih baik arahkan ke controller jika ada logika tambahan:
    Route::get('/user', [AuthController::class, 'user']); // Jika Anda punya method user() di AuthController

    // Rute logout
    Route::post('/logout', [AuthController::class, 'logout']); // Contoh logout, pastikan AuthController ada

    // === TAMBAHKAN RUTE CHANGE PASSWORD DI SINI ===
    Route::post('/change-password', [UserController::class, 'changePassword']);

    // Resource routes for User
    Route::apiResource('users', UserController::class);

    // Resource routes for transaction
    Route::apiResource('transactions', TransactionController::class); // Perhatikan 'transactions' (plural) untuk konsistensi

    Route::get('reports/transactions/{year}/{month}', [TransactionController::class, 'getMonthlyReport']); 
    
    //Resource routes for MonthlyReport
    Route::apiResource('monthly-reports', MonthlyReportController::class);
});