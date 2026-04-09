<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index']);
Route::post('/buy', [TicketController::class, 'buy']);
Route::get('/pay/{id}', [TicketController::class, 'pay']);
Route::post('/callback', [TicketController::class, 'callback'])->name('payment.callback');
Route::get('/success/{id}', [TicketController::class, 'success']);
Route::post('/sandbox/pay/{ticket}', [TicketController::class, 'sandboxPay'])->name('payment.sandbox.pay');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}', [AdminController::class, 'showTicket'])->name('tickets.show');
    Route::delete('/tickets/{ticket}', [AdminController::class, 'destroyTicket'])->name('tickets.destroy');
    Route::get('/scan', [AdminController::class, 'scanForm'])->name('scan');
    Route::post('/scan', [AdminController::class, 'scan'])->name('scan.process');
});
