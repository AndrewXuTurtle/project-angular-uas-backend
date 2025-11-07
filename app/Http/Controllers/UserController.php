<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Admin bisa lihat semua users.
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();
        
        // Jika bukan admin, return forbidden
        if ($currentUser->level !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access user list'
            ], 403);
        }
        
        // Semua user bisa akses semua business unit, jadi tampilkan semua users
        $users = User::all();
        
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'level' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'password' => 'sometimes|string|min:6',
            'level' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
