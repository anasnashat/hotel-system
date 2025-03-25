<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Stripe;

class StripeController extends Controller
{
    public function index(Room $room)
    {
        return view('checkout', compact('room'));
    }

    public function createCharge(Request $request)
    {



        try {
            $room = Room::with('reservations')->findOrFail($request->room_id);
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
//            dd($room->reservations()->isReserved()->get());

            // Check if the room is already reserved
            if ($room->reservations()->isReserved()->get()) {
                return back()->with('error', 'The room is already reserved.');
            }
            if ($room->capacity < $request->accompany_number) {
                return back()->with('error', 'The number of accompanying persons exceeds the room capacity.');
            }

            $status = Stripe\Charge::create([
                "amount" => $room->price,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Room reservation for " . $room->name
            ]);

            if ($status->status == 'succeeded') {
                // Create reservation
                $reservation = $room->reservations()->create([
                    'client_id' => auth()->id(),
                    'is_reserved' => true,
                    'accompany_number' => $request->accompany_number,
                    'price_at_booking' => $room->price,
                    'payment_intent_id' => $status->id,
                    'status' => 'confirmed'
                ]);

                Payment::create([
                    'reservation_id' => $reservation->id,
                    'payment_intent_id' => $status->id,
                    'payment_method_id' => $status->payment_method,
                ]);

                return redirect()->route('reservations.index')->with('success', 'Room reserved successfully!');
            }

            return back()->with('error', 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }}
