<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;



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

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:admin|manager|receptionist'])->name('dashboard');

Route::get('/waiting-approval', function () {
    return Inertia::render('auth/WaitingApproval');
})->name('waiting-approval');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
