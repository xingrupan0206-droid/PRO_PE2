<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('/registreren', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/registreren', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin,tandarts,mondhygienist,assistent')->group(function () {
        Route::get('/patienten', [PatientController::class, 'index'])->name('patients.index');
    });
});
