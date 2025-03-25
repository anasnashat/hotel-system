<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Stripe;

class StripeController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart->all();
//        dd($cartItems);
        return view('checkout', compact('cartItems'));
    }

    public function createCharge(Request $request)
    {
        try {
            $cartItems = auth()->user()->cart->all();
            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $room = Room::with('reservations')->find($cartItem->room_id);

                if (!$room) {
                    return back()->with('error', 'Room not found.');
                }

                if ($room->reservations()->isReserved()->exists()) {
                    return back()->with('error', "The room with number {$room->number} is already reserved.");
                }

                if ($room->capacity < $cartItem->accompany_number) {
                    return back()->with('error', 'The number of accompanying persons exceeds the room capacity.');
                }

                $totalAmount += $room->price;
            }

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $status = Stripe\Charge::create([
                "amount" => $totalAmount * 100, // Stripe expects the amount in cents
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Room reservation for user " . auth()->user()->name
            ]);

            if ($status->status == 'succeeded') {
                foreach ($cartItems as $cartItem) {
                    $room = Room::find($cartItem->room_id);

                    // Create reservation
                    $reservation = $room->reservations()->create([
                        'client_id' => auth()->id(),
                        'is_reserved' => true,
                        'accompany_number' => $cartItem->accompany_number,
                        'price_at_booking' => $room->price,
                        'payment_intent_id' => $status->id,
                        'status' => 'confirmed'
                    ]);

                    Payment::create([
                        'reservation_id' => $reservation->id,
                        'payment_intent_id' => $status->id,
                        'payment_method_id' => $status->payment_method,
                    ]);
                }

                Cart::whereIn('id', $cartItems->pluck('id'))->delete();

                return redirect()->route('reservations.index')->with('success', 'Rooms reserved successfully!');
            }

            return back()->with('error', 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
