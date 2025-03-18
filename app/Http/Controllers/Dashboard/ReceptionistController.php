<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ReceptionistController extends Controller
{
    public function index()
    {
        $requests = UserProfile::with('user')->where('approved_by', null)->get();
        return inertia('Dashboard/Receptionist/Index', [
            'requests' => $requests
        ]);
    }

    public function approve(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id'
        ]);

        $userProfile = UserProfile::where('user_id', $request->client_id)->first();
        $userProfile->approved_by = auth()->user()->id;
        $userProfile->approved_at = now();
        $userProfile->save();

        return redirect()->back()->with('success', 'Client approved successfully');
    }
}
