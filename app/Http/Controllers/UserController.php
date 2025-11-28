<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        $currentUser = $request->user();
        
        // Only admin can update users
        if ($currentUser->level !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Validasi semua field
        $validated = $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'password' => 'sometimes|string|min:6',
            'full_name' => 'sometimes|string|max:255',
            'level' => 'sometimes|in:admin,user',
            'is_active' => 'sometimes|boolean',
            'business_unit_ids' => 'sometimes|array',
            'business_unit_ids.*' => 'integer|exists:business_units,id',
            'menu_ids' => 'sometimes|array',
            'menu_ids.*' => 'integer|exists:menus,id'
        ]);

        DB::beginTransaction();
        try {
            // Update basic fields
            if ($request->has('username')) {
                $user->username = $request->username;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            if ($request->has('full_name')) {
                $user->full_name = $request->full_name;
            }
            if ($request->has('level')) {
                $user->level = $request->level;
            }
            if ($request->has('is_active')) {
                $user->is_active = $request->is_active;
            }
            
            $user->save();

            // Sync relationships
            if ($request->has('business_unit_ids')) {
                $user->businessUnits()->sync($request->business_unit_ids);
            }

            if ($request->has('menu_ids')) {
                $user->menus()->sync($request->menu_ids);
            }

            DB::commit();

            // Reload dengan relationships
            $user->load(['businessUnits', 'menus']);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => new UserResource($user)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * @OA\Get(
     *     path="/api/users/{id}/access",
     *     tags={"Users"},
     *     summary="Get user access data for dropdown (V4 Critical API)",
     *     description="Returns user info + accessible business units + menus in one call. Perfect untuk populate dropdown di Angular.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="User ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="username", type="string", example="user1"),
     *                     @OA\Property(property="level", type="string", example="user")
     *                 ),
     *                 @OA\Property(property="business_units", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="business_unit", type="string", example="Batam")
     *                     )
     *                 ),
     *                 @OA\Property(property="menus", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nama_menu", type="string", example="Dashboard"),
     *                         @OA\Property(property="url_link", type="string", example="/dashboard")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     * 
     * Get user detail with accessible business units and menus for dropdown.
     * Returns user info + list of BUs and menus user has access to.
     */
    public function getAccessData(string $id)
    {
        $user = User::with(['businessUnits', 'menus'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'level' => $user->level,
                    'is_active' => $user->is_active,
                ],
                'business_units' => $user->businessUnits->map(function($bu) {
                    return [
                        'id' => $bu->id,
                        'business_unit' => $bu->business_unit,
                        'active' => $bu->active,
                    ];
                }),
                'menus' => $user->menus->map(function($menu) {
                    return [
                        'id' => $menu->id,
                        'nama_menu' => $menu->nama_menu,
                        'url_link' => $menu->url_link,
                        'parent' => $menu->parent,
                    ];
                }),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/business-units",
     *     tags={"Users"},
     *     summary="Assign business units to user",
     *     description="Assign array of business unit IDs to user. This will REPLACE existing assignments.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="User ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"business_unit_ids"},
     *             @OA\Property(
     *                 property="business_unit_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business units assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business units updated successfully")
     *         )
     *     )
     * )
     * 
     * Assign business units to user (replace existing).
     */
    public function assignBusinessUnits(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'business_unit_ids' => 'required|array',
            'business_unit_ids.*' => 'exists:business_units,id',
        ]);

        // Sync will replace all existing relationships
        $user->businessUnits()->sync($validated['business_unit_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Business units updated successfully',
            'data' => [
                'user_id' => $user->id,
                'business_units' => $user->businessUnits,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/menus",
     *     tags={"Users"},
     *     summary="Assign menus to user",
     *     description="Assign array of menu IDs to user. This will REPLACE existing assignments.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="User ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"menu_ids"},
     *             @OA\Property(
     *                 property="menu_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 7}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Menus assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Menus updated successfully")
     *         )
     *     )
     * )
     * 
     * Assign menus to user (replace existing).
     */
    public function assignMenus(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'menu_ids' => 'required|array',
            'menu_ids.*' => 'exists:menus,id',
        ]);

        // Sync will replace all existing relationships
        $user->menus()->sync($validated['menu_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Menus updated successfully',
            'data' => [
                'user_id' => $user->id,
                'menus' => $user->menus,
            ]
        ]);
    }
}
