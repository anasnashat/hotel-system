<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ReceptionistController extends Controller
{
    public function index()
    {
        $requests = UserProfile::with('user')->whereHas('user.roles', function ($query) {
            $query->where('name', '=', 'client');
        })->where('approved_by', '=', null)->get();

//        dd($requests);
        return inertia('Receptionist/Index', [
            'requests' => $requests
        ]);
    }

    public function showReservation()
    {
        $clients = Reservation::with(['client.profile', 'room'])
            ->whereHas('client.profile', function ($query) {
                $query->whereNotNull('approved_by')
                    ->where('approved_by', auth()->id());
            })
            ->get();

//        dd($clients);
        return inertia('Receptionist/ShowReservation', [
            'clients' => $clients
        ]);
    }


    public function approve(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id'
        ]);

        $userProfile = UserProfile::findOrFail($request->client_id);
        $userProfile->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
//        dd($userProfile);


        return back()->with([
            'success' => 'Client approved successfully',
        ]);
    }


}
