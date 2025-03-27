<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $managers = User::with('profile')
            ->whereHas('roles', function ($query) {
                $query->where('name', '=', 'manager');
            })
            ->paginate(10);

        return Inertia::render('Dashboard/Admin/Index', [
            'managers' => $managers,
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
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8',
        'national_id' => 'required|string|max:255',
        'avatar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $avatarPath = null;
    if ($request->hasFile('avatar_image')) {
        $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'avatar_image' => $avatarPath,
    ]);

    $user->profile()->create([
        'national_id' => $request->national_id,
    ]);

    $user->assignRole('manager');

    return redirect()->back()->with('success', 'Manager created successfully.');
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
    public function update(Request $request, User $manager)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $manager->id,
            'password' => 'nullable|string|min:8',
            'national_id' => 'sometimes|required|string|max:255',
            'avatar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $avatarPath = $manager->avatar_image;

        if ($request->hasFile('avatar_image')) {
            // Delete old avatar if exists
            if ($manager->avatar_image && Storage::disk('public')->exists($manager->avatar_image)) {
                Storage::disk('public')->delete($manager->avatar_image);
            }
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
        }


        // Update the user
        $manager->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $manager->password,
            'avatar_image' => $avatarPath,
        ]);

        // Update the profile
        $manager->profile()->update([
            'national_id' => $request->national_id,
        ]);

        return redirect()->back()->with('success', 'Manager updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $manager)
    {
        $manager->delete();
        if ($manager->avatar_image){
            Storage::disk('public')->delete($manager->avatar_image);
        }
        return redirect()->back()->with('success', 'Manager deleted successfully.');
    }
}
