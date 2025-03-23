<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = UserProfile::findOrFail($id);

        $request = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user->id,
            'phone_number' => 'required|string|max:255',
            'national_id' => 'required|string|max:255|unique:user_profiles,national_id,' . $id,

        ]);
        $user->user->update([
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
        $user->update([
            'phone_number' => $request['phone_number'],
            'national_id' => $request['national_id'],
        ]);
        return back()->with([
            'success' => 'Client updated successfully',
        ]);

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
