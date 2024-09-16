<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AdminController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wallets', [WalletController::class, 'create']);
    Route::post('/wallets/{id}/credit', [WalletController::class, 'credit']);
    Route::post('/wallets/transfer', [WalletController::class, 'transfer']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin routes
    Route::post('/admin/transfers/{id}/approve', [AdminController::class, 'approveTransfer']);
    Route::get('/admin/summary/{month}', [AdminController::class, 'monthlySummary']);

    // Paystack Callback
    Route::get('/paystack/callback', [WalletController::class, 'verifyCredit'])->name('paystack.callback');
});

