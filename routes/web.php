<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('test/', [App\Http\Controllers\Dashboard\TestController::class, 'index']);
Route::group(['prefix' => 'receptionist'], function () {
    Route::get('/', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'index']);
    Route::post('approve', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'approve'])->name('receptionist.approve');
    Route::get('show-reservation', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'showReservation'])->name('receptionist.show-reservation');
});


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
