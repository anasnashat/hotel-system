<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Room;
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
            return redirect()->back()->with(['message' => 'Unauthorized'], 401);
        }

        $exists = Favorite::where('room_id', $request->room_id)
            ->where('user_id', $user)
            ->exists();

        if ($exists) {
            return redirect()->back()->with(['message' => 'Room already in favorites'], 409);
        }

        $favorite = Favorite::create([
            'room_id' => $request->room_id,
            'user_id' => $user,
        ]);

        return redirect()->back()->with(['message' => 'Room added to favorites', 'data' => $favorite], 201);
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
            return redirect()->back()->with(['message' => 'Favorite not found'], 404);
        }

        $favorite->delete();

        return redirect()->back()->with(['message' => 'Favorite removed successfully'], 200);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'accompany_number' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $room = Room::findOrFail($request->room_id);

            if ($request->accompany_number > $room->capacity) {
                return redirect()->back()->with([
                    'message' => 'Number of guests exceeds room capacity (Max: ' . $room->capacity . ')'
                ], 422);
            }

            // Check if the same room already exists in cart
            $existingCart = Cart::where('room_id', $request->room_id)
                ->where('user_id', $userId)
                ->first();

            if ($existingCart) {
                // If accompany number is the same, return conflict
                if ($existingCart->accompany_number == $request->accompany_number) {
                    return redirect()->back()->with([
                        'message' => 'This room with the same guest count is already in your cart'
                    ], 409);
                }


                // Check if new count exceeds capacity
                if ($request->accompany_number > $room->capacity) {
                    return redirect()->back()->with([
                        'message' => 'Updated guest count exceeds room capacity (Max: ' . $room->capacity . ')'
                    ], 422);
                }

                // Update the existing cart item
                $existingCart->update([
                    'accompany_number' => $request->accompany_number
                ]);

                $message = 'Cart item updated successfully';
            } else {
                // Create new cart item
                Cart::create([
                    'room_id' => $request->room_id,
                    'accompany_number' => $request->accompany_number
                ]);

                $message = 'Room added to cart successfully';
            }

            DB::commit();
            return redirect()->back()->with(['message' => $message], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'message' => 'Failed to update cart',
                'error' => $e->getMessage()
            ], 500);
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
            return redirect()->back()->with(['message' => 'Room not found in cart'], 404);
        }

        $cart->delete();

        return redirect()->back()->with(['message' => 'Room removed from cart successfully'], 200);
    }
}
