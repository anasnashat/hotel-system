<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\ManagerController;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\CountryController;


Route::get('test/', [App\Http\Controllers\Dashboard\TestController::class, 'index']);
//Route::group(['prefix' => 'receptionist'], function () {
//    Route::get('/', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'index'])->name('receptionist.index');
//    Route::post('approve', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'approve'])->name('receptionist.approve');
//    Route::get('show-reservation', [App\Http\Controllers\Dashboard\ReceptionistController::class, 'showReservation'])->name('receptionist.show-reservation');
//});

// =============================================== This is the routes for the floor CRUD ==============================================================
Route::resource('floors', App\Http\Controllers\FloorController::class)->middleware(['auth', 'role:admin|manager']);
// =============================================== End ==================================================================================================

// =============================================== This is the routes for the floor CRUD ==============================================================
Route::middleware(['auth', 'role:admin|manager'])->group(function () {
Route::resource('rooms', RoomController::class)->except(['update', 'destroy']);
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('room.owner')->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('room.owner')->name('rooms.destroy');
    Route::delete('/rooms/{room}/images/{image}', [RoomController::class, 'deleteImage'])
        ->middleware('room.owner')
        ->name('rooms.images.destroy');
});

// =============================================== End ==================================================================================================

Route::resource('clients-management', App\Http\Controllers\Dashboard\ClientManagementController::class)->middleware(['auth', 'role:admin|manager']);
Route::post('approve', [App\Http\Controllers\Dashboard\ClientManagementController::class, 'approve'])->name('client.approve');
Route::get('show-reservation', [App\Http\Controllers\Dashboard\ClientManagementController::class, 'showReservation'])->name('receptionist.show-reservation');

Route::middleware(['auth', 'role:admin|manager'])->prefix('manager')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'index'])->name('manager.dashboard');
    Route::get('/manage-clients', [ManagerController::class, 'manageClients'])->name('manager.manage-clients');
    Route::get('/clients/{id}/edit', [ManagerController::class, 'editClient'])->name('manager.edit-client');
    Route::put('/clients/{id}/update', [ManagerController::class, 'updateClient'])->name('manager.update-client');
    Route::delete('/clients/{id}/delete', [ManagerController::class, 'deleteClient'])->name('manager.delete-client');
    Route::post('/clients/approve', [ManagerController::class, 'approve'])->name('manager.approve-client');
    // Routes for managing receptionists
    Route::get('/manage-receptionists', [ManagerController::class, 'manageReceptionists'])->name('manager.manage-receptionists');
    Route::put('/receptionists/{id}/update', [ManagerController::class, 'updateReceptionist'])->name('manager.update-receptionist');
    Route::delete('/receptionists/{id}/delete', [ManagerController::class, 'deleteReceptionist'])->name('manager.delete-receptionist');
    Route::post('/manager/store-receptionist', [ManagerController::class, 'storeReceptionist'])->name('manager.store-receptionist');
    Route::post('/receptionists/{id}/ban', [ManagerController::class, 'banReceptionist'])->name('manager.ban-receptionist');
    Route::post('/receptionists/{id}/unban', [ManagerController::class, 'unbanReceptionist'])->name('manager.unban-receptionist');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth');

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');


//This route to check if email,phone,national_id already exits
Route::post('/check-existence', function (Request $request) {
    $field = $request->input('field');
    $value = $request->input('value');

    // Determine which field to check
    $exists = false;
    if ($field === 'email') {
        $exists = User::where('email', $value)->exists();
    } elseif ($field === 'phone_number') {
        $exists = UserProfile::where('phone_number', $value)->exists();
    } elseif ($field === 'national_id') {
        $exists = UserProfile::where('national_id', $value)->exists();
    }

    return response()->json(['exists' => $exists]);
});

Route::get('/cart', function () {
    return Inertia::render('CartComponent');
})->name('cart');

Route::get('/favorites', function () {
    return Inertia::render('Favorites');
})->name('favorites');


Route::post('/logout', function (Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');



// Route::get('/login', function () {
//     return Inertia::render('Dashboard');
// })->name('login');

// //Admin Dashboard
Route::get('/admin/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

//Manager Dashboard
Route::get('/manager/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:manager'])->name('manager.dashboard');

//Receptionist Dashboard
Route::get('/receptionist/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:receptionist'])->name('receptionist.dashboard');

//Client Dashboard redirect to home
Route::get('/client/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified', 'role:client'])->name('client.dashboard');

// country api list
Route::get('/countries', [CountryController::class, 'getCountries']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:admin|manager|receptionist'])->name('dashboard');

Route::get('/waiting-approval', function () {
    return Inertia::render('auth/WaitingApproval');
})->name('waiting-approval');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
