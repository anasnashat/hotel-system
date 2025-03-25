<?php
namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->favorites()->with('room')->get());
    }

    public function store(Room $room)
    {
        Auth::user()->favorites()->create(['room_id' => $room->id]);

        return response()->json(['message' => 'Added to favorites']);
    }

    public function destroy(Room $room)
    {
        Auth::user()->favorites()->where('room_id', $room->id)->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }
}
