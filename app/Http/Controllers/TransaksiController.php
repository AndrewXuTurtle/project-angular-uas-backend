<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BusinessUnit;
use App\Http\Resources\TransaksiResource;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     * Auto-filter by business unit of logged in user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Get business unit dari token
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan login ulang.',
                'data' => []
            ], 403);
        }
        
        $businessUnitId = $token->business_unit_id;
        
        // Query transaksi berdasarkan business unit dari token
        $query = Transaksi::with(['businessUnit', 'user'])
            ->byBusinessUnit($businessUnitId);
        
        // Filter by status jika ada
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        // Jika bukan admin, hanya tampilkan transaksi user sendiri
        if ($user->level !== 'admin') {
            $query->byUser($user->id);
        }
        
        $transaksis = $query->latest()->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi retrieved successfully',
            'data' => TransaksiResource::collection($transaksis)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Get business unit dari token
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan login ulang.'
            ], 403);
        }
        
        $businessUnitId = $token->business_unit_id;
        
        $validated = $request->validate([
            'kode_transaksi' => 'required|string|max:50|unique:transaksis,kode_transaksi',
            'nama_transaksi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'status' => 'sometimes|in:pending,approved,rejected',
            'keterangan' => 'nullable|string',
        ]);
        
        // Auto-set business_unit_id dan user_id dari token & user yang login
        $validated['business_unit_id'] = $businessUnitId;
        $validated['user_id'] = $user->id;
        
        $transaksi = Transaksi::create($validated);
        $transaksi->load(['businessUnit', 'user']);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi created successfully',
            'data' => new TransaksiResource($transaksi)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Get business unit dari token
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan login ulang.'
            ], 403);
        }
        
        $businessUnitId = $token->business_unit_id;
        
        $transaksi = Transaksi::with(['businessUnit', 'user'])->find($id);
        
        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi not found'
            ], 404);
        }
        
        // Validasi: transaksi harus dalam business unit yang sama
        if ($transaksi->business_unit_id !== $businessUnitId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this transaksi'
            ], 403);
        }
        
        // Jika bukan admin, hanya bisa lihat transaksi sendiri
        if ($user->level !== 'admin' && $transaksi->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this transaksi'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi retrieved successfully',
            'data' => new TransaksiResource($transaksi)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Get business unit dari token
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan login ulang.'
            ], 403);
        }
        
        $businessUnitId = $token->business_unit_id;
        
        $transaksi = Transaksi::find($id);
        
        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi not found'
            ], 404);
        }
        
        // Validasi: transaksi harus dalam business unit yang sama
        if ($transaksi->business_unit_id !== $businessUnitId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this transaksi'
            ], 403);
        }
        
        // Jika bukan admin, hanya bisa update transaksi sendiri
        if ($user->level !== 'admin' && $transaksi->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this transaksi'
            ], 403);
        }
        
        $validated = $request->validate([
            'kode_transaksi' => 'sometimes|string|max:50|unique:transaksis,kode_transaksi,' . $id,
            'nama_transaksi' => 'sometimes|string|max:255',
            'jumlah' => 'sometimes|numeric|min:0',
            'tanggal' => 'sometimes|date',
            'status' => 'sometimes|in:pending,approved,rejected',
            'keterangan' => 'nullable|string',
        ]);
        
        $transaksi->update($validated);
        $transaksi->load(['businessUnit', 'user']);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi updated successfully',
            'data' => new TransaksiResource($transaksi)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        // Get business unit dari token
        if (!$token || !$token->business_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Business unit tidak ditemukan. Silakan login ulang.'
            ], 403);
        }
        
        $businessUnitId = $token->business_unit_id;
        
        $transaksi = Transaksi::find($id);
        
        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi not found'
            ], 404);
        }
        
        // Validasi: transaksi harus dalam business unit yang sama
        if ($transaksi->business_unit_id !== $businessUnitId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this transaksi'
            ], 403);
        }
        
        // Hanya admin yang bisa delete
        if ($user->level !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can delete transaksi'
            ], 403);
        }
        
        $transaksi->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi deleted successfully'
        ]);
    }
}
