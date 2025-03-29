<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['media'])
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'price' => $room->price,
                    'number' => $room->number,
                    'is_available' => $room->is_available,
                    'first_image_url' => $room->getFirstMediaUrl('rooms_image') ?? [],
                ];
            });

        return Inertia::render('Home', [
                    'rooms' => $rooms,
                ]);
    }

}
