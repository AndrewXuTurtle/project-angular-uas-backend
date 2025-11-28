<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        // Sorting: allow limited, whitelisted columns and direction
        $allowedSorts = ['name', 'email', 'phone', 'created_at', 'updated_at'];
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        // Admin bisa filter berdasarkan business_unit_id via query parameter
        if ($request->has('business_unit_id')) {
            $businessUnitId = $request->business_unit_id;

            $query = Customer::where('business_unit_id', $businessUnitId)->with('businessUnit');
            $customers = $query->orderBy($sortBy, $sortDir)->get();

            return response()->json([
                'success' => true,
                'message' => 'Customers retrieved successfully',
                'data' => CustomerResource::collection($customers)
            ]);
        }

        // Regular user flow - harus select business unit dulu
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan pilih business unit terlebih dahulu.'
            ], 403);
        }
        
        // Get selected business unit name
        $selectedBU = \App\Models\BusinessUnit::find($token->business_unit_id);
        
        if (!$selectedBU) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak valid.'
            ], 403);
        }
        
        // Get customers that belong to ANY business unit with the same name (location)
        $query = Customer::whereHas('businessUnit', function($query) use ($selectedBU) {
                $query->where('business_unit', $selectedBU->business_unit);
            })->with('businessUnit');

        $customers = $query->orderBy($sortBy, $sortDir)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'data' => CustomerResource::collection($customers)
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'business_unit_id' => 'required|exists:business_units,id'
        ]);
        
        $businessUnitId = $validated['business_unit_id'];
        
        // Validasi business unit exists
        $businessUnit = \App\Models\BusinessUnit::find($businessUnitId);
        
        if (!$businessUnit) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan pilih business unit terlebih dahulu.'
            ], 403);
        }
        
        // Jika user bukan admin, validasi bahwa mereka punya akses ke BU ini
        if ($user->level !== 'admin') {
            // Cek apakah user punya akses ke business unit ini
            $hasAccess = $user->businessUnits()->where('business_units.id', $businessUnitId)->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke business unit ini'
                ], 403);
            }
        }
        
        // Admin bisa create untuk semua BU
        // User biasa hanya bisa create untuk BU yang mereka punya akses
        $customer = Customer::create($validated);
        $customer->load('businessUnit');
        
        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => new CustomerResource($customer)
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan pilih business unit terlebih dahulu.'
            ], 403);
        }
        
        $selectedBU = \App\Models\BusinessUnit::find($token->business_unit_id);
        
        if (!$selectedBU) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak valid.'
            ], 403);
        }
        
        $customer = Customer::find($id);
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }
        
        // Check if customer's business unit location matches selected location
        if ($customer->businessUnit->business_unit !== $selectedBU->business_unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this customer'
            ], 403);
        }
        
        $customer->load('businessUnit');
        
        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer)
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $customer = Customer::find($id);
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }
        
        // Check access: admin bisa akses semua, user biasa hanya BU mereka
        if ($user->level !== 'admin') {
            // Cek apakah user punya akses ke BU customer ini
            $hasAccess = $user->businessUnits()
                ->where('business_units.id', $customer->business_unit_id)
                ->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke customer ini'
                ], 403);
            }
        }
        
        // Validasi - TANPA business_unit_id (customer tidak bisa pindah BU)
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);
        
        // Update hanya field yang diizinkan (TANPA business_unit_id)
        $customer->update($request->only(['name', 'email', 'phone', 'address']));
        $customer->load('businessUnit');
        
        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diupdate',
            'data' => new CustomerResource($customer)
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $customer = Customer::findOrFail($id);

            // ADMIN: can delete any customer without BU selection
            if ($user->level === 'admin') {
                $customer->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil dihapus'
                ]);
            }

            // USER: only delete if customer.business_unit_id is in user's BU list
            $userBusinessUnitIds = $user->businessUnits->pluck('id')->toArray();

            if (!in_array($customer->business_unit_id, $userBusinessUnitIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus customer ini'
                ], 403);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/customers/bulk-delete",
     *     tags={"Customers"},
     *     summary="Bulk delete customers",
     *     description="Delete multiple customers at once. All authenticated users can perform this action.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(
     *                 property="ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="Array of customer IDs to delete"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulk delete successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="3 customers deleted successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="deleted_count", type="integer", example=3),
     *                 @OA\Property(property="failed_count", type="integer", example=0),
     *                 @OA\Property(property="deleted_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *                 @OA\Property(property="failed_ids", type="array", @OA\Items(type="integer"), example={})
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Business unit not selected or unauthorized access"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     * 
     * Bulk delete customers
     */
    public function bulkDelete(Request $request)
    {
        $user = $request->user();

        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:customers,id',
            ]);

            $ids = $validated['ids'];

            // ADMIN: delete any customers
            if ($user->level === 'admin') {
                $deletedCount = Customer::whereIn('id', $ids)->delete();

                return response()->json([
                    'success' => true,
                    'message' => "{$deletedCount} customer berhasil dihapus",
                    'deleted_count' => $deletedCount
                ]);
            }

            // USER: only delete customers in user's business units
            $userBusinessUnitIds = $user->businessUnits->pluck('id')->toArray();

            $customers = Customer::whereIn('id', $ids)
                ->whereIn('business_unit_id', $userBusinessUnitIds)
                ->get();

            $deletedCount = $customers->count();
            $requestedCount = count($ids);

            if ($deletedCount > 0) {
                Customer::whereIn('id', $customers->pluck('id'))->delete();
            }

            if ($deletedCount < $requestedCount) {
                $deniedCount = $requestedCount - $deletedCount;
                return response()->json([
                    'success' => true,
                    'message' => "{$deletedCount} customer berhasil dihapus, {$deniedCount} customer tidak memiliki akses",
                    'deleted_count' => $deletedCount,
                    'denied_count' => $deniedCount
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} customer berhasil dihapus",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus customers: ' . $e->getMessage()
            ], 500);
        }
    }
}
