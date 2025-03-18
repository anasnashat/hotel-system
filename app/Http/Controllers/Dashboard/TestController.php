<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $requests = UserProfile::with('user')->where('approved_by', null)->get();
        $user = $requests[0];
        dd($user);
//
//        $user->update(['approved_by' => 5]);
//        dd($user);
//        dd($requests);

//        $userId = 5;
//        $user = User::with('approvedUsers.user')->where('id', $userId)->first();
//
//        dd($user);
    }
}
