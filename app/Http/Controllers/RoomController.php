<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoomController extends Controller
{



    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $floors = Floor::all();
        $rooms = Room::with(['floor', 'createdBy', 'media'])
        ->paginate(10)
            ->through(function ($room) {
                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'capacity' => $room->capacity,
                    'description' => $room->description,
                    'price' => $room->price,
                    'floor_name' => $room->floor->name ?? 'N/A',
                    'manager_name' => $room->createdBy->name,
                    'manager_id' => $room->createdBy->id,
                    'floor_id' => $room->floor_id,
                    'images' => $room->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'url' => $media->getUrl(),
                        ];
                    }),
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
    try {
        DB::beginTransaction();

        $validated = $request->validated();
        $validated['price'] = $validated['price'] * 100;
        $room = Room::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $room->addMedia($image)
                    ->preservingOriginal()
                    ->usingFileName($image->getClientOriginalName())
                    ->toMediaCollection('rooms_image', 'public');
            }
        }

        DB::commit();
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while creating the room');
    }
}

    /**
     * Display the specified resource.
     */
public function show($id)
{
    try {
        DB::beginTransaction();
        $room = Room::findOrFail($id);
        DB::commit();

        return Inertia::render('Dashboard/Room/Show', [
            'room' => $room,
            'images' => $room->getMedia('rooms_image')->map(function ($media) {
                return $media->getUrl();
            }),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while loading the room');
    }
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
    try {
        DB::beginTransaction();

        $room = Room::findOrFail($id);
        $validated = $request->validated();
        $validated['price'] = $validated['price'] * 100;

        $room->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $room->addMedia($image)
                    ->preservingOriginal()
                    ->usingFileName($image->getClientOriginalName())
                    ->toMediaCollection('rooms_image', 'public');
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Room updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while updating the room');
    }
}

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    try {
        DB::beginTransaction();
        $room = Room::findOrFail($id);
        if ($room->reservations()->count() > 0){
            return redirect()->back()->with('error', 'Room has reservations, cannot deleted');
        }
        $room->delete();
        $room->clearMediaCollection('rooms_image');
        DB::commit();
        return redirect()->back()->with('success', 'Room deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while deleting the room');
    }
}


public function deleteImage($roomId, $imageId)
{
    try {
        DB::beginTransaction();

        $room = Room::findOrFail($roomId);

        $media = $room->getMedia('rooms_image')->find($imageId);

        if ($media) {
            $media->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Image deleted successfully.');
        }

        DB::rollBack();
        return redirect()->back()->with('error', 'Image could not be deleted');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while deleting the image');
    }
}
}
