<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin,tandarts,mondhygienist,assistent')->group(function () {
        Route::get('/facturen', [InvoiceController::class, 'index'])->name('invoices.index');
    });
});
