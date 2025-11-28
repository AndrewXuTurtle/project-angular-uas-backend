<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use App\Http\Resources\BusinessUnitResource;

class BusinessUnitController extends Controller
{
    /**
     * Display a listing of active business units (PUBLIC - for login page).
     */
    public function publicList()
    {
        $businessUnits = BusinessUnit::where('active', 'y')->get();
        
        return response()->json([
            'success' => true,
            'data' => BusinessUnitResource::collection($businessUnits)
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businessUnits = BusinessUnit::with('users')->get();
        return response()->json([
            'success' => true,
            'data' => BusinessUnitResource::collection($businessUnits)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_unit' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'active' => 'required|in:y,n',
        ]);

        $businessUnit = BusinessUnit::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business Unit berhasil dibuat',
            'data' => new BusinessUnitResource($businessUnit)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $businessUnit = BusinessUnit::with('user')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new BusinessUnitResource($businessUnit)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);

        $validated = $request->validate([
            'business_unit' => 'sometimes|string',
            'user_id' => 'sometimes|exists:users,id',
            'active' => 'sometimes|in:y,n',
        ]);

        $businessUnit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business Unit berhasil diupdate',
            'data' => new BusinessUnitResource($businessUnit)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);
        $businessUnit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business Unit berhasil dihapus'
        ]);
    }
}
