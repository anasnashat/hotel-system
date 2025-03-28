<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserItemsController extends Controller
{
    public function addFavorite(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        if (!auth()->check()) {
            return back()->with('error', 'Please login to add favorites');
        }

        $exists = Favorite::where('room_id', $request->room_id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'This room is already in your favorites');
        }

        Favorite::create([
            'room_id' => $request->room_id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Room added to favorites successfully');
    }

    public function removeFavorite(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $favorite = Favorite::where('room_id', $request->room_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $favorite->delete();

        return back()->with('success', 'Room removed from favorites');
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'accompany_number' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $room = Room::findOrFail($request->room_id);

            if ($request->accompany_number > $room->capacity) {
                return back()->with('error',
                    "Number of guests exceeds room capacity (Max: {$room->capacity})"
                );
            }

            if (!$room->is_available) {
                return back()->with('error', 'This room is already reserved');
            }

            $existingCart = Cart::where('room_id', $request->room_id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingCart) {
                if ($existingCart->accompany_number == $request->accompany_number) {
                    return back()->with('info', 'This room is already in your cart');
                }

                $existingCart->update([
                    'accompany_number' => $request->accompany_number
                ]);
                $message = 'Cart updated successfully';
            } else {
                Cart::create([
                    'room_id' => $request->room_id,
                    'accompany_number' => $request->accompany_number
                ]);
                $message = 'Room added to cart successfully';
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update cart: '.$e->getMessage());
        }
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $cart = Cart::where('room_id', $request->room_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $cart->delete();

        return back()->with('success', 'Room removed from cart');
    }
}
