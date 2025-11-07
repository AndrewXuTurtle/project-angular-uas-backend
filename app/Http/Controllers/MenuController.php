<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Resources\MenuResource;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with(['parentMenu', 'children'])->get();
        return response()->json([
            'success' => true,
            'data' => MenuResource::collection($menus)
        ]);
    }

    /**
     * Get menu tree structure
     */
    public function tree()
    {
        $menus = Menu::whereNull('parent')
            ->with('allChildren')
            ->get();

        return response()->json([
            'success' => true,
            'data' => MenuResource::collection($menus)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string',
            'url_link' => 'required|string',
            'parent' => 'nullable|exists:menus,id',
        ]);

        $menu = Menu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dibuat',
            'data' => new MenuResource($menu)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $menu = Menu::with(['parentMenu', 'children'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new MenuResource($menu)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'nama_menu' => 'sometimes|string',
            'url_link' => 'sometimes|string',
            'parent' => 'nullable|exists:menus,id',
        ]);

        $menu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diupdate',
            'data' => new MenuResource($menu)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
