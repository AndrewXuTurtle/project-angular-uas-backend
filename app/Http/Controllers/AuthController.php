<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessUnit;
use App\Http\Resources\BusinessUnitResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'business_unit_id' => 'required|exists:business_units,id',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif'
            ], 403);
        }

        // Validasi business unit
        $businessUnit = BusinessUnit::find($request->business_unit_id);
        if (!$businessUnit || $businessUnit->active !== 'y') {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak valid atau tidak aktif'
            ], 400);
        }

        // Hapus token lama
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth-token');
        
        // Set business_unit_id di token
        $token->accessToken->business_unit_id = $businessUnit->id;
        $token->accessToken->save();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => new UserResource($user),
                'business_unit' => new BusinessUnitResource($businessUnit),
                'token' => $token->plainTextToken
            ]
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    /**
     * Get user privileges with allowed menus and permissions
     */
    public function getUserPrivileges(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Get business unit dari token
        $businessUnit = null;
        if ($token && $token->business_unit_id) {
            $businessUnit = BusinessUnit::find($token->business_unit_id);
        }
        
        // Load user privileges
        $user->load(['privilegeUsers.menu']);
        
        // Format privileges untuk Angular sidebar
        $menus = $user->privilegeUsers
            ->filter(fn($privilege) => $privilege->allowed) // Only allowed menus
            ->map(function ($privilege) {
                return [
                    'id' => $privilege->menu->id,
                    'nama_menu' => $privilege->menu->nama_menu,
                    'url_link' => $privilege->menu->url_link,
                    'parent' => $privilege->menu->parent,
                    'allowed' => $privilege->allowed,
                    'permissions' => [
                        'c' => $privilege->c,
                        'r' => $privilege->r,
                        'u' => $privilege->u,
                        'd' => $privilege->d,
                    ]
                ];
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'business_unit' => $businessUnit ? new BusinessUnitResource($businessUnit) : null,
                'menus' => $menus
            ]
        ]);
    }

    /**
     * Switch business unit without logout
     */
    public function switchBusinessUnit(Request $request)
    {
        $request->validate([
            'business_unit_id' => 'required|exists:business_units,id',
        ]);

        $user = $request->user();
        
        // Validasi business unit
        $businessUnit = BusinessUnit::find($request->business_unit_id);
        if (!$businessUnit || $businessUnit->active !== 'y') {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak valid atau tidak aktif'
            ], 400);
        }

        // Hapus token lama
        $user->tokens()->delete();

        // Buat token baru dengan business unit baru
        $token = $user->createToken('auth-token');
        $token->accessToken->business_unit_id = $businessUnit->id;
        $token->accessToken->save();

        return response()->json([
            'success' => true,
            'message' => 'Business unit berhasil diganti ke ' . $businessUnit->business_unit,
            'data' => [
                'business_unit' => new BusinessUnitResource($businessUnit),
                'token' => $token->plainTextToken
            ]
        ]);
    }
}
