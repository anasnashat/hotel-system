<?php

use App\Http\Controllers\Dashboard\AdminManagementController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Dashboard\ManagerController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->prefix('dashboard')->group(function () {

    Route::get('test/', [App\Http\Controllers\Dashboard\TestController::class, 'index']);
    //Route::group(['prefix' => 'receptionist'], function () {
    //    Route::get('/', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'index'])->name('receptionist.index');
    //    Route::post('approve', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'approve'])->name('receptionist.approve');
    //    Route::get('show-reservation', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'showReservation'])->name('receptionist.show-reservation');
    //});

    // =============================================== This is the routes for the floor CRUD ==============================================================
    Route::resource('floors', App\Http\Controllers\FloorController::class)->middleware([ 'role:admin|manager']);
    // =============================================== End ==================================================================================================

    // =============================================== This is the routes for the floor CRUD ==============================================================
    Route::middleware([ 'role:admin|manager'])->group(function () {
        Route::resource('rooms', RoomController::class)->except(['update', 'destroy']);
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('room.owner')->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('room.owner')->name('rooms.destroy');
        Route::delete('/rooms/{room}/images/{image}', [RoomController::class, 'deleteImage'])
            ->middleware('room.owner')
            ->name('rooms.images.destroy');
    });

    // =============================================== End ==================================================================================================

    Route::resource('clients-management', App\Http\Controllers\Dashboard\ClientManagementController::class)->middleware([ 'role:admin|manager|receptionist']);
    Route::post('approve', [App\Http\Controllers\Dashboard\ClientManagementController::class, 'approve'])->name('client.approve');
    Route::get('show-reservation', [App\Http\Controllers\Dashboard\ClientManagementController::class, 'showReservation'])->name('receptionist.show-reservation');
    Route::get('all-clients', [App\Http\Controllers\Dashboard\ClientManagementController::class, 'allClients'])->name('receptionist.all-clients')->middleware([ 'role:admin|manager']);
    Route::get('my-approved-clients', [App\Http\Controllers\Dashboard\ClientManagementController::class, 'myApprovedClients'])->name('receptionist.myApprovedClients');

    // =============================================== End ==================================================================================================

    Route::resource('managers', AdminManagementController::class)->middleware([ 'role:admin|manager']);;
    // =============================================== End ==================================================================================================



    Route::middleware([ 'role:admin|manager'])->prefix('manager')->group(function () {

        Route::get('/manage-receptionists', [ManagerController::class, 'manageReceptionists'])->name('manager.manage-receptionists');
        Route::put('/receptionists/{id}/update', [ManagerController::class, 'updateReceptionist'])->name('manager.update-receptionist');
        Route::delete('/receptionists/{id}/delete', [ManagerController::class, 'deleteReceptionist'])->name('manager.delete-receptionist');
        Route::post('/manager/store-receptionist', [ManagerController::class, 'storeReceptionist'])->name('manager.store-receptionist');
        Route::post('/receptionists/{id}/ban', [ManagerController::class, 'banReceptionist'])->name('manager.ban-receptionist');
        Route::post('/receptionists/{id}/unban', [ManagerController::class, 'unbanReceptionist'])->name('manager.unban-receptionist');
    });
});
