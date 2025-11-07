<?php

namespace App\Http\Controllers;

use App\Models\PrivilegeUser;
use Illuminate\Http\Request;
use App\Http\Resources\PrivilegeUserResource;

class PrivilegeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $privileges = PrivilegeUser::with(['user', 'menu'])->get();
        return response()->json([
            'success' => true,
            'data' => PrivilegeUserResource::collection($privileges)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'menu_id' => 'required|exists:menus,id',
            'c' => 'boolean',
            'r' => 'boolean',
            'u' => 'boolean',
            'd' => 'boolean',
        ]);

        $privilege = PrivilegeUser::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Privilege berhasil dibuat',
            'data' => new PrivilegeUserResource($privilege)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $privilege = PrivilegeUser::with(['user', 'menu'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new PrivilegeUserResource($privilege)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $privilege = PrivilegeUser::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'menu_id' => 'sometimes|exists:menus,id',
            'c' => 'sometimes|boolean',
            'r' => 'sometimes|boolean',
            'u' => 'sometimes|boolean',
            'd' => 'sometimes|boolean',
        ]);

        $privilege->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Privilege berhasil diupdate',
            'data' => new PrivilegeUserResource($privilege)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $privilege = PrivilegeUser::findOrFail($id);
        $privilege->delete();

        return response()->json([
            'success' => true,
            'message' => 'Privilege berhasil dihapus'
        ]);
    }
}
