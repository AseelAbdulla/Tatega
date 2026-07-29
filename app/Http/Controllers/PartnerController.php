<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $partners = Partner::all();

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب الشركاء بنجاح',
        'data' => $partners
    ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|array',
        'logo' => 'required|string|max:255',
        'website_url' => 'nullable|string|max:255',
        'sort_order' => 'nullable|integer',
        'status' => 'nullable|string|max:50',
        'lat' => 'nullable|numeric',
        'lng' => 'nullable|numeric',
    ]);

    $partner = Partner::create($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم إضافة الشريك بنجاح',
        'data' => $partner
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $partner = Partner::findOrFail($id);

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب الشريك بنجاح',
        'data' => $partner
    ], 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $partner = Partner::findOrFail($id);

    $validated = $request->validate([
        'name' => 'nullable|array',
        'logo' => 'sometimes|string|max:255',
        'website_url' => 'nullable|string|max:255',
        'sort_order' => 'nullable|integer',
        'status' => 'nullable|string|max:50',
        'lat' => 'nullable|numeric',
        'lng' => 'nullable|numeric',
    ]);

    $partner->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم تحديث الشريك بنجاح',
        'data' => $partner
    ], 200);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $partner = Partner::findOrFail($id);

    $partner->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'تم حذف الشريك بنجاح'
    ], 200);
}
}
