<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManagerController extends Controller
{

    public function manageClients()
    {
        $clients = UserProfile::with('user')->whereHas('user.roles', function ($query) {
            $query->where('name', '=', 'client');
        })->get();

        return inertia('Manager/Manageclients', [
            'clients' => $clients,
        ]);
    }
    public function index(){
        return Inertia::render('manger.dashboard');
    }

    public function updateClient(Request $request, $id)
    {
        $client = UserProfile::with('user')->findOrFail($id);
        $user = $client->user;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);


        return redirect()->back()->with('success', 'Client updated successfully.');
    }



    public function deleteClient($id)
    {
        $client = UserProfile::find($id);
        $client->delete();

        return back()->with('success', 'Client deleted successfully.');
    }

    public function editClient($id)
    {
        $client = UserProfile::with('user')->findOrFail($id);
        dd($client);
        return inertia('Manager/EditClient', [
            'client' => $client,
        ]);
    }


    public function approve(Request $request)
{
    $request->validate([
        'client_id' => 'required|exists:user_profiles,id'
    ]);

    $userProfile = UserProfile::findOrFail($request->client_id);
    $userProfile->update([
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return back()->with('success', 'Client approved successfully!');
}



public function manageReceptionists()
{
    $receptionists = User::with("profile.createdBy")->whereHas('roles', function ($query) {
        $query->where('name', '=', 'receptionist');
    })->get()->map(function ($receptionist) {
        $receptionist->is_banned = $receptionist->isbanned();
        return $receptionist;

    });

    return inertia('Manager/ManageReceptionists', [
        'receptionists' => $receptionists,
    ]);
}



public function updateReceptionist(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'national_id' => 'required|string|max:255',
        'avatar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = User::findOrFail($id);

    $avatarPath = $user->avatar_image;
    if ($request->hasFile('avatar_image')) {
        $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
    }

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'avatar_image' => $avatarPath,
    ]);

    $user->profile()->update([
        'national_id' => $request->national_id,
    ]);

    return redirect()->back()->with('success', 'Receptionist updated successfully.');
}

    public function deleteReceptionist($id)
    {
        $receptionist = User::find($id);
        $receptionist->delete();

        return back()->with('success', 'Receptionist deleted successfully.');
    }
    
    public function storeReceptionist(Request $request)
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
            'created_by'  => auth()->id(),   
        ]);
    
        $user->assignRole('receptionist');
    
        return redirect()->back()->with('success', 'Manager created successfully.');
    }


    public function banReceptionist($id)
    {
        $receptionist = User::find($id);
        $receptionist->ban();

        return back()->with('success', 'Receptionist banned successfully.');
    }

    public function unbanReceptionist($id)
    {
        $receptionist = User::find($id);
        $receptionist->unban();

        return back()->with('success', 'Receptionist unbanned successfully.');
    }


}
