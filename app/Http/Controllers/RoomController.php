<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Floor;
use App\Models\Room;
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $floors = Floor::all();
        $rooms = Room::with(['floor', 'createdBy'])
            ->paginate(100)
            ->through(function ($room) {
                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'capacity' => $room->capacity,
                    'description' => $room->description,
                    'price' => $room->price,
                    'floor_name' => $room->floor->name ?? 'N/A',
                    'manager_name' => $room->createdBy->name,
                    'floor_id' => $room->floor_id,
                    'images' => $room->getMedia('rooms_image')->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'url' => $media->getUrl(),
                        ];
                    }),
//                    'images' => $room->getMedia('rooms_image')->map(function ($media) {
//                        return $media->getUrl();
//                    }),
                    'first_image_url' => $room->getFirstMediaUrl('rooms_image'),
                    ];
            });

        return Inertia::render('Dashboard/Room/Index', [
            'rooms' => $rooms,
            'floors' => $floors,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $room = Room::findOrFail(12);
        return view('room.create', compact('room'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $room->addMedia($image)
                    ->preservingOriginal()
                    ->usingFileName($image->getClientOriginalName()) // Use the original file name
                    ->toMediaCollection('rooms_image', 'public'); // Save to the custom directory
            }
        }

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $room = Room::findOrFail($id);
        return Inertia::render('Dashboard/Room/Show', [
            'room' => $room,
            'images' => $room->getMedia('rooms_image')->map(function ($media) {
                return $media->getUrl();
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(UpdateRoomRequest $request, $id)
{
//    dd($request);
    $room = Room::findOrFail($id);
    $room->update($request->validated());

    if ($request->hasFile('images')) {
        // Clear existing media before adding new ones
//        $room->clearMediaCollection('rooms_image');

        foreach ($request->file('images') as $image) {
            $room->addMedia($image)
                ->preservingOriginal()
                ->usingFileName($image->getClientOriginalName())
                ->toMediaCollection('rooms_image', 'public');
        }
    }
    return redirect()->back()->with('success', 'Room updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
    }


    public function deleteImage($roomId, $imageId)
    {
        $room = Room::findOrFail($roomId);
        $media = $room->getMedia('rooms_image')->find($imageId);

        if ($media) {
            $media->delete();
            return redirect()->back()->with('success' , 'Image deleted successfully.');
        }

        return response()->json(['error' => 'Image not found.'], 404);
    }
}
