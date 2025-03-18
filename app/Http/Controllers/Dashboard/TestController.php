<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {

//        $clients = Reservation::with('user.reservations')->where('approved_by', '=', '5')->get();
//        dd($clients);

//        $requests = UserProfile::with('user')->whereHas('user.roles', function($query) {
//            $query->where('name', 'client');
//        })->where('approved_by', '=', null)->get();
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

    $clients = Reservation::with(['client.profile' => function($query) {
            $query->where('approved_by', '=', auth()->user()->id);
        }, 'room'])->get();
        dd($clients);

    }
}
