<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreReceptionistRequest;
use App\Http\Requests\UpdateReceptionistRequest;
class ManagerController extends Controller
{

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

public function updateReceptionist(UpdateReceptionistRequest $request, $id)
{
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


    public function storeReceptionist(StoreReceptionistRequest $request)
    {   $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        ]);
        $user->profile()->create([
        'national_id' => $request->national_id,
        'created_by'  => auth()->id(),   
    ]);
    $user->assignRole('receptionist');
    return redirect()->back()->with('success', 'Receptionist created successfully.');
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
        $receptionist->ban();
        return back()->with('success', 'Receptionist banned successfully.');
    }

    public function unbanReceptionist($id)
    {   $receptionist = User::find($id);
        $receptionist->unban();
        return back()->with('success', 'Receptionist unbanned successfully.');
    }


}
