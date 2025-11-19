<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClampingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;


// Route::post('/auth/login', [AuthController::class, 'login']);
// Route::get('/auth/login', [AuthController::class, 'loginIndex'])->name('auth.login');
// Route::post('/auth/register', [AuthController::class, 'register']);
// // Route::get('/auth/register', [AuthController::class, 'registerIndex'])->name('auth.register');
// // Route::get('/auth', [AuthController::class, 'index'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Route::post('/clampings', [ClampingController::class, 'store']); 
// Route::get('/clampings', [ClampingController::class, 'index'])->name('clampings');

// Route::post('/payments', [PaymentController::class, 'store']);
// Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
// Route::get('/dashboard', [PaymentController::class, 'dashboard'])->name('dashboard');
