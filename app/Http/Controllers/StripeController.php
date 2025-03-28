<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Stripe;
use Inertia\Inertia;
use Inertia\Response;

class StripeController extends Controller
{
    public function index()
    {
        // $cartItems = auth()->user()->cart()->get();
//        dd($cartItems);
//        dd($cartItems);
        // return view('checkout', compact('cartItems'));
     return Inertia::render('checkout', [
        'cartItems' => Cart::with('room')->get(),
        'stripeKey' => config('services.stripe.key'),
    ]);
    }

   public function createCharge(Request $request)
   {
       try {
           $cartItems = auth()->user()->cart()->get();
           if ($cartItems->isEmpty()) {
               return back()->with('error', 'Your cart is empty.');
           }

           $totalAmount = 0;

           // Begin transaction
           return \DB::transaction(function () use ($cartItems, $request, &$totalAmount) {
               // Lock the rooms for update to prevent concurrent reservations
               $roomIds = $cartItems->pluck('room_id')->toArray();
               $rooms = Room::whereIn('id', $roomIds)->lockForUpdate()->get();

               foreach ($cartItems as $cartItem) {
                   $room = $rooms->find($cartItem->room_id);

                   if (!$room) {
                       return back()->with('error', 'Room not found.');
                   }

                   if (!$room->is_available) {
                       return back()->with('error', "The room with number {$room->number} is already reserved.");
                   }

                   if ($room->capacity < $cartItem->accompany_number) {
                       return back()->with('error', 'The number of accompanying persons exceeds the room capacity.');
                   }

                   $totalAmount += $room->price;
               }

               // Process payment
               Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

               $status = Stripe\Charge::create([
                   "amount" => $totalAmount,
                   "currency" => "usd",
                   "source" => $request->stripeToken,
                   "description" => "Room reservation for user " . auth()->user()->name
               ]);

               if ($status->status == 'succeeded') {
                   foreach ($cartItems as $cartItem) {
                       $room = $rooms->find($cartItem->room_id);

                       // Create reservation
                       $reservation = $room->reservations()->create([
                           'client_id' => auth()->id(),
                           'accompany_number' => $cartItem->accompany_number,
                           'price_at_booking' => $room->price,
                           'payment_intent_id' => $status->id,
                           'status' => 'confirmed'
                       ]);
                       $room->update(['is_available' => 0]);

                       Payment::create([
                           'reservation_id' => $reservation->id,
                           'payment_intent_id' => $status->id,
                           'payment_method_id' => $status->payment_method,
                       ]);
                   }

                   // Clear the cart
                   Cart::whereIn('id', $cartItems->pluck('id'))->delete();

                   return Inertia::location(route('success.page'));
                }

               return back()->with('error', 'Payment failed. Please try again.');
           });
       } catch (\Exception $e) {
           return back()->with('error', 'Something went wrong: ' . $e->getMessage());
       }
   }
}
