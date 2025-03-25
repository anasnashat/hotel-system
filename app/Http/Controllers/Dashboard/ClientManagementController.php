<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Support\Facades\Notification;

class ClientManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    public function allClients()
    {
        $clients = UserProfile::with('user')->whereHas('user.roles', function ($query) {
            $query->where('name', '=', 'client');
        })->get();

//        dd($requests);
        return inertia('Receptionist/AllClients', [
            'clients' => $clients
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

        try {
            DB::beginTransaction();
            $userProfile = UserProfile::findOrFail($request->client_id);
            $userProfile->update([
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            DB::commit();
            // $client = User::findOrFail($request->client_id);
            $client = $userProfile->user;
            // dd($client);
            $client->notify(new ClientApprovedNotification($client));
            // Notification::send($client, new ClientApprovedNotification($client));

            return back()->with([
                'success' => 'Client approved successfully',
            ]);
        } catch (\Exception){
            DB::rollBack();
            return back()->with([
                'error' => 'Something went wrong',
            ]);
        }

//        dd($userProfile);
    }

    public function myApprovedClients()
    {
        $clients = UserProfile::with('user')->whereHas('user.roles', function ($query) {
            $query->where('name', '=', 'client');
        })->where('approved_by', '=', auth()->id())->paginate(10);

        return inertia('Receptionist/MyApprovedClients', [
            'clients' => $clients
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

   
    public function update(UpdateClientRequest $request, string $id)
    {
        $user = UserProfile::findOrFail($id);

        try {
            DB::beginTransaction();
            $user->user->update([
                'name' => $request['name'],
                'email' => $request['email'],
            ]);
            $user->update([
                'phone_number' => $request['phone_number'],
                'national_id' => $request['national_id'],
            ]);
            DB::commit();
            return back()->with([
                'success' => 'Client updated successfully',
            ]);
        } catch (\Exception $e){
            DB::rollBack();
            return back()->with([
                'error' => 'Something went wrong',
            ]);
        }


    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            DB::beginTransaction();
            $user= User::findOrFail($id);
            $user->delete();
            DB::commit();
            return back()->with([
                'success' => 'Client deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with([
                'error' => 'Something went wrong',
            ]);
        }


    }
}
