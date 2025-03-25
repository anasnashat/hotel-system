<?php


use App\Http\Controllers\Api\UserItemsController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Room;
use App\Http\Controllers\CartController;
use App\Http\Resources\RoomResource;
use App\Http\Controllers\FavoriteController;



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
});

Route::get('/rooms/{id}', function ($id) {
    return new RoomResource(Room::findOrFail($id));
});





Route::get('/rooms', function (Request $request) {
    return RoomResource::collection(Room::paginate(10)); // Paginated response
});

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
    Route::get('/favorites', [FavoriteController::class, 'index']); // List all favorites
    Route::post('/favorites/{room}', [FavoriteController::class, 'store']); // Add favorite
    Route::delete('/favorites/{room}', [FavoriteController::class, 'destroy']);
});
