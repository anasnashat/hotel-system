<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('test/', [App\Http\Controllers\Dashboard\TestController::class, 'index']);
Route::group(['prefix' => 'receptionist'], function () {
    Route::get('/', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'index'])->name('receptionist.index');
    Route::post('approve', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'approve'])->name('receptionist.approve');
    Route::get('show-reservation', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'showReservation'])->name('receptionist.show-reservation');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth');

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:admin|manager|receptionist'])->name('dashboard');


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
