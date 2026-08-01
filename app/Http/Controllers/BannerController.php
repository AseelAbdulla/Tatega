<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::all();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب البنرات بنجاح',
            'data' => $banners
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image_path' => 'required|string|max:255',
            'slogan' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        $banner = Banner::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة البنر بنجاح',
            'data' => $banner
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $banner = Banner::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب البنر بنجاح',
            'data' => $banner
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'image_path' => 'sometimes|string|max:255',
            'slogan' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        $banner->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث البنر بنجاح',
            'data' => $banner
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);

        $banner->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف البنر بنجاح'
        ], 200);
    }
}
