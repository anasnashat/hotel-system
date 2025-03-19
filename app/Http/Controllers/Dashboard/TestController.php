<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\UserProfile;
use App\Models\User;
class TestController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
//        $clients = Reservation::with('user.reservations')->where('approved_by', '=', '5')->get();
//        dd($clients);

        // $requests = UserProfile::with('user')->whereHas('user.roles', function ($query) {
        //     $query->where('name', 'client');
        // })->where('approved_by', '!=', null)->get();
        // foreach ($requests as $request) {
        //     $request->update(['approved_by' => null]);
        // }
=======

        $clients = UserProfile::with('user.reservations')->where('approved_by', '=', '5')->get();

        dd($clients[0]->user->isBanned());


//        $requests = UserProfile::with('user')->whereHas('user.roles', function ($query) {
//            $query->where('name', 'client');
//        })->where('approved_by', '!=', null)->get();
//        foreach ($requests as $request) {
//            $request->update(['approved_by' => null]);
//        }
>>>>>>> 85d8e864779d99c20891502464891d1f7e8bc231
//        dd($requests);
//
//        $user->update(['approved_by' => 5]);
//        dd($user);
//        dd($requests);

//        $userId = 5;
//        $user = User::with('approvedUsers.user')->where('id', $userId)->first();
//
//        dd($user);


//       $floor = Floor::create([
//            'number' => 1555995,
//            'name' => 'first floor',
//            'created_by' => 2
//       ]);
//       $room = Room::create([
//          'number' => 5555,
//           'capacity' => 2,
//           'price' => 1000,
//           'description' => 'first room',
//           'floor_id' => $floor->id,
//           'created_by' => 2
//       ]);
//       dd($room);
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

<<<<<<< HEAD
        // $clients = Reservation::with(['client.profile', 'room'])->whereHas('client.profile', function ($query) {
        //     $query->whereNotNull('approved_by')
        //         ->where('approved_by', '=', 5);
        // })->get();
        // dd($clients);
=======
//        $clients = Reservation::with(['client.profile', 'room'])->whereHas('client.profile', function ($query) {
//            $query->whereNotNull('approved_by')
//                ->where('approved_by', '=', 5);
//        })->get();
//        dd($clients);
>>>>>>> 85d8e864779d99c20891502464891d1f7e8bc231
//        $clients = Reservation::with(['client.profile' => function($query) {
//            $query->whereNotNull('approved_by')->where('approved_by', '=', auth()->user()->id);
//        }, 'room'])->get();
//        dd($clients);

   $user=User::find(21);
   $user->assignRole('manager');
    }
}
