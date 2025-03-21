<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'min:8', 'confirmed', Rules\Password::defaults()],
            'gender' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'national_id' => 'required|string|unique:user_profiles',
            'country' => 'required|string',
            'country_code' => 'required|string',
            'phone_number' => 'required|string|unique:user_profiles',
        ]);

        // Check if an avatar file was uploaded and store it in the public storage under 'avatars'
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Create a new user in the users table
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'avatar_image' => $avatarPath,
        ]);

        // Create a user profile linked to the newly created user
        $user1 = UserProfile::create([
            'user_id' => $user->id,
            'national_id' => $request->national_id,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'created_by' => $user->id,
        ]);

        // Assign the "client" role to the newly registered user
        $user->assignRole("client");

        // Fire the Registered event to handle any post-registration actions (e.g., sending a verification email)
        event(new Registered($user));

        return to_route('waiting-approval');
    }
}
