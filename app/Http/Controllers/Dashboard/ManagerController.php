<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function updateClient(Request $request, $id)
    {
        $client = User::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
        ]);
    
        $client->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
    
        return redirect()->route('manager.manage-clients')->with('success', 'Client updated successfully.');
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
        $receptionists = User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'receptionist');
        })->get();

        return inertia('Manager/ManageReceptionists', [
            'receptionists' => $receptionists,
        ]);
    }

    public function updateReceptionist(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $receptionist = User::find($id);
        $receptionist->update($request->only(['name', 'email']));

        return back()->with('success', 'Receptionist updated successfully.');
    }

    public function deleteReceptionist($id)
    {
        $receptionist = User::find($id);
        $receptionist->delete();

        return back()->with('success', 'Receptionist deleted successfully.');
    }

    public function banReceptionist($id)
    {
        $receptionist = User::find($id);
        $receptionist->update(['is_banned' => true]);

        return back()->with('success', 'Receptionist banned successfully.');
    }

    public function unbanReceptionist($id)
    {
        $receptionist = User::find($id);
        $receptionist->update(['is_banned' => false]);

        return back()->with('success', 'Receptionist unbanned successfully.');
    }
}