<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFloorRequest;
use App\Http\Requests\UpdateFloorRequest;
use App\Models\Floor;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FloorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $floors = Floor::with('createdBy')->paginate(10);
        return Inertia::render('Dashboard/Floor/Index', [
            'floors' => $floors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Floor/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFloorRequest $request)
    {
        try {
            DB::beginTransaction();
            Floor::create($request->validated());
            DB::commit();
            return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $floor = Floor::findOrFail($id);
        return Inertia::render('Dashboard/Floor/Show', [
            'floor' => $floor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $floor = Floor::findOrFail($id);
        return Inertia::render('Dashboard/Floor/Edit', [
            'floor' => $floor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFloorRequest $request, $id)
    {
        $floor = Floor::findOrFail($id);
        try {
            DB::beginTransaction();
            $floor->update($request->validated());
            DB::commit();
            return redirect()->route('floors.index')->with('success', 'Floor updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $floor = Floor::findOrFail($id);
        try {
            DB::beginTransaction();
            $floor->delete();
            DB::commit();
            return redirect()->route('floors.index')->with('success', 'Floor deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
