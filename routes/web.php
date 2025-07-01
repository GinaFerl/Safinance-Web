<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])
    ->name('users.index');

    Route::post('users', [RegisteredUserController::class, 'store']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
