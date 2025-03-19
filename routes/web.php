<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\ManagerController;


Route::get('test/', [App\Http\Controllers\Dashboard\TestController::class, 'index']);
Route::group(['prefix' => 'receptionist'], function () {
    Route::get('/', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'index'])->name('receptionist.index');
    Route::post('approve', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'approve'])->name('receptionist.approve');
    Route::get('show-reservation', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'showReservation'])->name('receptionist.show-reservation');
});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'index'])->name('manager.dashboard');
    Route::get('/manage-clients', [ManagerController::class, 'manageClients'])->name('manager.manage-clients');
    Route::get('/clients/{id}/edit', [ManagerController::class, 'editClient'])->name('manager.edit-client');
    Route::put('/clients/{id}/update', [ManagerController::class, 'updateClient'])->name('manager.update-client');
    Route::delete('/clients/{id}/delete', [ManagerController::class, 'deleteClient'])->name('manager.delete-client');
    Route::post('/clients/approve', [ManagerController::class, 'approve'])->name('manager.approve-client');
    // Routes for managing receptionists
    Route::get('/manage-receptionists', [ManagerController::class, 'manageReceptionists'])->name('manager.manage-receptionists');
    Route::post('/receptionists/{id}/update', [ManagerController::class, 'updateReceptionist'])->name('manager.update-receptionist');
    Route::delete('/receptionists/{id}/delete', [ManagerController::class, 'deleteReceptionist'])->name('manager.delete-receptionist');
    Route::post('/receptionists/{id}/ban', [ManagerController::class, 'banReceptionist'])->name('manager.ban-receptionist');
    Route::post('/receptionists/{id}/unban', [ManagerController::class, 'unbanReceptionist'])->name('manager.unban-receptionist');
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
