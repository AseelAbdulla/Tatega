<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $features = Feature::all();

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب المميزات بنجاح',
        'data' => $features
    ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'icon' => 'required|string|max:100',
        'title' => 'required|array',
        'description' => 'required|array',
        'sort_order' => 'nullable|integer',
        'status' => 'nullable|string|max:50',
    ]);

    $feature = Feature::create($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم إضافة الميزة بنجاح',
        'data' => $feature
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $feature = Feature::findOrFail($id);

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب الميزة بنجاح',
        'data' => $feature
    ], 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $feature = Feature::findOrFail($id);

    $validated = $request->validate([
        'icon' => 'sometimes|string|max:100',
       'title' => 'nullable|array',
       'description' => 'nullable|array',
        'sort_order' => 'nullable|integer',
        'status' => 'nullable|string|max:50',
    ]);

    $feature->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم تحديث الميزة بنجاح',
        'data' => $feature
    ], 200);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $feature = Feature::findOrFail($id);

    $feature->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'تم حذف الميزة بنجاح'
    ], 200);
}
}
