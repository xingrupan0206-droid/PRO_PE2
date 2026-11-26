<?php

use App\Http\Controllers\AfspraakOverzichtController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
    Route::get('/afspraken', [AfspraakOverzichtController::class, 'index'])
        ->middleware('can:view-appointment-overview')
        ->name('afspraken.overzicht');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
