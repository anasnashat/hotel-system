<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserItemsController extends Controller
{
    public function addFavorite(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $user = auth()->id();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $exists = Favorite::where('room_id', $request->room_id)
            ->where('user_id', $user)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Room already in favorites'], 409);
        }

        $favorite = Favorite::create([
            'room_id' => $request->room_id,
            'user_id' => $user,
        ]);

        return response()->json(['message' => 'Room added to favorites', 'data' => $favorite], 201);
    }



    public function removeFavorite(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $favorite = Favorite::where('room_id', $request->room_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Favorite removed successfully'], 200);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        try {
            DB::beginTransaction();

            $cart = Cart::where('room_id', $request->room_id)
                ->where('user_id', auth()->id())
                ->first();

            if ($cart) {
                return response()->json(['message' => 'Room already added to cart'], 400);
            }

            Cart::create([
                'room_id' => $request->room_id,
            ]);

            DB::commit();
            return response()->json(['message' => 'Room added to cart successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $cart = Cart::where('room_id', $request->room_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart) {
            return response()->json(['message' => 'Room not found in cart'], 404);
        }

        $cart->delete();

        return response()->json(['message' => 'Room removed from cart successfully'], 200);
    }
}
