<?php


use App\Http\Controllers\Api\UserItemsController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    return response()->json([
        'token' => $user->createToken($request->device_name)->plainTextToken
    ]);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites', [UserItemsController::class, 'addFavorite'])->name('favorites.add');
    Route::delete('/favorites', [UserItemsController::class, 'removeFavorite'])->name('favorites.remove');

    Route::post('/cart', [UserItemsController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart', [UserItemsController::class, 'removeFromCart'])->name('cart.remove');
});
