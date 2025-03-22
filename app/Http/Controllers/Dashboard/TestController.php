<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\UserProfile;

class TestController extends Controller
{
    public function index()
    {

//        $clients = UserProfile::with('user.reservations')->where('approved_by', '=', '5')->get();

//        dd($clients[0]->user->isBanned());

//        $user = User::findOrFail(5);
//        dd($user->isBanned());


//        $requests = UserProfile::with('user')->whereHas('user.roles', function ($query) {
//            $query->where('name', 'client');
//        })->where('approved_by', '!=', null)->get();
//        foreach ($requests as $request) {
//            $request->update(['approved_by' => null]);
//        }
//        dd($requests);
//
//        $user->update(['approved_by' => 5]);
//        dd($user);
//        dd($requests);

//        $userId = 5;
//        $user = User::with('approvedUsers.user')->where('id', $userId)->first();
//
//        dd($user);

//
//       $floor = Floors::create([
//            'number' => 1555995,
//            'name' => 'first floor',
//            'created_by' => 2
//       ]);


//       $room = Room::create([
//           'capacity' => 2,
//           'price' => 1000,
//           'description' => 'first room',
//           'floor_id' => 10,
//       ]);
        $room = Room::findOrFail(24);


       dd($room);


// Retrieve stored images
        $media = $room->getMedia('room_images');
        dd($media);

//        $reservation = Reservation::create([
//            'reservation_code'=> 5555,
//            'client_id'=>11,
//            'room_id'=>1,
//            'check_in_date'=>'2021-09-09',
//            'check_out_date'=>'2021-09-10',
//            'accompany_number'=>1,
//            'price_at_booking'=>2000,
//            'payment_intent_id'=>'payment_intent_id',
//            'payment_method_id'=>'sssss'
//        ]);
//        dd($reservation);

//        $clients = Reservation::with(['client.profile', 'room'])->whereHas('client.profile', function ($query) {
//            $query->whereNotNull('approved_by')
//                ->where('approved_by', '=', 5);
//        })->get();
//        dd($clients);
//        $clients = Reservation::with(['client.profile' => function($query) {
//            $query->whereNotNull('approved_by')->where('approved_by', '=', auth()->user()->id);
//        }, 'room'])->get();
//        dd($clients);

    }
}
