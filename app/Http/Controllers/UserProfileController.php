<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        return Inertia::render('UserDashboardComponent', [
            'user' => $user,
            'profile' => $profile
        ]);    }

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
    public function store(StoreUserProfileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserProfile $userProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserProfile $userProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserProfileRequest $request, string $id)
    {
        $user = Auth::user();
        // Ensure user is updating their own profile or they have admin privileges
        if ($user->id != $id) {
            abort(403, 'Unauthorized action.');
        }
        // Update user information
        $user->update([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
        ]);
        // Update or create user profile
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
    
         if ($request->hasFile('avatar')) {
        // Delete old avatar if exists
        if ($user->avatar_image) {
            Storage::disk('public')->delete($user->avatar_image);
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_image' => $avatarPath]);
    }
    
        $profile->save();
    
        return back()->with([
            'success' => 'Profile updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserProfile $userProfile)
    {
        //
    }
}
