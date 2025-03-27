<?php

use App\Http\Controllers\Api\UserItemsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StripeController;
use App\Models\Cart;
use App\Models\Reservation;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\UserProfileController;
use App\Models\Room;


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

Route::get('/room/{id}', function ($id) {
    $room = Room::findOrFail($id);
    return Inertia::render('RoomDetails', ['id' => $id]); // ✅ Pass 'id'
})->name('room.details');



Route::get('/room/{id}', function ($id) {
    return Inertia::render('RoomDetails', ['id' => $id]);
})->name('room');

//Route::get('/booking-status', function () {
//
//    return Inertia::render('BookingStatus');
//})->name('booking.status')->middleware('auth'); // Ensure only logged-in users can view it


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

Route::middleware(['auth'])->group(function () {
    Route::get('/cart', function () {
        $cartItems = Cart::with('room')->where("user_id", auth()->id())->get();
        return Inertia::render('CartComponent', ['cartItems' => $cartItems]);
    })->name('cart');
});

Route::get('/favorites', function () {
    return Inertia::render('Favorites');
})->name('favorites');

Route::middleware(['auth'])->group(function () {
    // Explicitly define the dashboard route
    Route::get('/userdashboard', [UserProfileController::class, 'index'])->name('userdashboard');
    Route::get('/userdashboard/{id}/edit', [UserProfileController::class, 'edit'])->name('userdashboard.edit');
    Route::post('/userdashboard/{id}/update', [UserProfileController::class, 'update'])->name('userdashboard.update');
});

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


Route::middleware(['auth'])->group(function (){
    Route::get('/cart', function () {

        $cartItems = Cart::with('room')->where("user_id" , auth()->user()->id)->get();
        return Inertia::render('CartComponent', ['cartItems' => $cartItems]);
    })->name('cart');
    Route::get('checkout/', [StripeController::class, 'index']);
    Route::post('checkout/create-charge', [StripeController::class, 'createCharge'])->name('stripe.create-charge');



    Route::post('cart/', [UserItemsController::class, 'addToCart']);
    Route::delete('cart/', [UserItemsController::class, 'removeFromCart']);

    Route::post('favorites/', [UserItemsController::class, 'addFavorite']);
    Route::delete('favorites/', [UserItemsController::class, 'removeFavorite']);

    Route::get('reservation/', [ReservationController::class, 'index'])->name('reservation.index');


});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';

