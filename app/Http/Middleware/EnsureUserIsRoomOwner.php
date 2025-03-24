<?php

namespace App\Http\Middleware;

use App\Models\Room;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsRoomOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roomId = $request->route('room');
        $room = Room::findOrFail($roomId);
        if (!$room || $room->created_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to  this action.');
        }
        return $next($request);
    }
}
