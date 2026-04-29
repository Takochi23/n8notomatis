<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AnalitikController;
use App\Http\Controllers\ScanStrukController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
Route::get('/analitik', [AnalitikController::class, 'index'])->name('analitik');
Route::get('/scanstruk', [ScanStrukController::class, 'index'])->name('scanstruk');
Route::post('/scanstruk/scan', [ScanStrukController::class, 'scan'])->name('scanstruk.scan');
Route::post('/scanstruk/save', [ScanStrukController::class, 'save'])->name('scanstruk.save');

// API Routes for Transactions (Supabase PostgreSQL)
Route::get('/ajax/transactions', [TransactionController::class, 'index']);
Route::post('/ajax/transactions', [TransactionController::class, 'store']);
Route::delete('/ajax/transactions/{id}', [TransactionController::class, 'destroy']);