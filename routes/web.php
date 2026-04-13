<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
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

Route::middleware('auth')->get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/events', [SuperAdminController::class, 'events'])->name('events');
    Route::get('/events/create', [SuperAdminController::class, 'createEvent'])->name('events.create');
    Route::post('/events', [SuperAdminController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{event}/edit', [SuperAdminController::class, 'editEvent'])->name('events.edit');
    Route::put('/events/{event}', [SuperAdminController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{event}', [SuperAdminController::class, 'destroyEvent'])->name('events.destroy');

    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/tickets/create', [SuperAdminController::class, 'createTicket'])->name('tickets.create');
    Route::post('/tickets', [SuperAdminController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/tickets/{ticket}/edit', [SuperAdminController::class, 'editTicket'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [SuperAdminController::class, 'updateTicket'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [AdminController::class, 'destroyTicket'])->name('tickets.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/sell', [AdminController::class, 'sellForm'])->name('sell');
    Route::post('/sell', [AdminController::class, 'sellStore'])->name('sell.store');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}', [AdminController::class, 'showTicket'])->name('tickets.show');
    Route::get('/scan', [AdminController::class, 'scanForm'])->name('scan');
    Route::post('/scan', [AdminController::class, 'scan'])->name('scan.process');
});
