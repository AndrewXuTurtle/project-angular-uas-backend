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
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Login user dan dapatkan token. Setelah login, user harus pilih business unit.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", example="user1"),
     *             @OA\Property(property="password", type="string", example="User123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login berhasil. Silakan pilih business unit."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="username", type="string", example="user1"),
     *                     @OA\Property(property="level", type="string", example="user")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Username atau password salah")
     * )
     * 
     * Login user WITHOUT business unit selection
     * User must call selectBusinessUnit after login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
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

        // Hapus token lama
        $user->tokens()->delete();

        // Buat token baru (tanpa business_unit_id dulu)
        $token = $user->createToken('auth-token');

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil. Silakan pilih business unit.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="User logout",
     *     description="Logout dan hapus token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout berhasil")
     *         )
     *     )
     * )
     * 
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();

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
     * @OA\Get(
     *     path="/api/user/business-units",
     *     tags={"Authentication"},
     *     summary="Get user's accessible business units",
     *     description="Ambil daftar business unit yang boleh diakses user (V4 - many-to-many)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business units yang boleh diakses"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="business_unit", type="string", example="Batam"),
     *                     @OA\Property(property="active", type="string", example="y")
     *                 )
     *             )
     *         )
     *     )
     * )
     * 
     * Get business units that user can access (V4 - many-to-many)
     */
    public function getUserBusinessUnits(Request $request)
    {
        $user = $request->user();
        
        // Get business units via many-to-many relationship
        $businessUnits = $user->businessUnits()->where('active', 'y')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Business units yang boleh diakses',
            'data' => BusinessUnitResource::collection($businessUnits)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/menus",
     *     tags={"Authentication"},
     *     summary="Get user's accessible menus",
     *     description="Ambil daftar menu yang boleh diakses user (V4 - many-to-many)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Menus yang boleh diakses"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama_menu", type="string", example="Dashboard"),
     *                     @OA\Property(property="url_link", type="string", example="/dashboard")
     *                 )
     *             )
     *         )
     *     )
     * )
     * 
     * Get menus that user can access (V4 - many-to-many)
     */
    public function getUserMenus(Request $request)
    {
        $user = $request->user();
        
        // Get menus via many-to-many relationship
        $menus = $user->menus;
        
        return response()->json([
            'success' => true,
            'message' => 'Menus yang boleh diakses',
            'data' => $menus
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/select-business-unit",
     *     tags={"Authentication"},
     *     summary="Select business unit",
     *     description="Pilih business unit setelah login. Token akan di-update dengan business_unit_id.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"business_unit_id"},
     *             @OA\Property(property="business_unit_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business unit berhasil dipilih",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business unit berhasil dipilih: Batam"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="business_unit", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="business_unit", type="string", example="Batam")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="User tidak punya akses ke business unit ini")
     * )
     * 
     * Select business unit after login (V4 - many-to-many)
     */
    public function selectBusinessUnit(Request $request)
    {
        $request->validate([
            'business_unit_id' => 'required|exists:business_units,id',
        ]);

        $user = $request->user();
        
        // Validasi: apakah user boleh akses business unit ini via many-to-many?
        $businessUnit = $user->businessUnits()
            ->where('business_units.id', $request->business_unit_id)
            ->where('active', 'y')
            ->first();
        
        if (!$businessUnit) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke business unit ini'
            ], 403);
        }

        // Update token dengan business_unit_id
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $token->business_unit_id = $businessUnit->id;
        $token->save();

        return response()->json([
            'success' => true,
            'message' => 'Business unit berhasil dipilih: ' . $businessUnit->business_unit,
            'data' => [
                'business_unit' => new BusinessUnitResource($businessUnit)
            ]
        ]);
    }

    /**
     * Switch business unit (same as select, for backward compatibility)
     */
    public function switchBusinessUnit(Request $request)
    {
        return $this->selectBusinessUnit($request);
    }
}
