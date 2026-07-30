<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $settings = Setting::all();

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب الإعدادات بنجاح',
        'data' => $settings
    ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'key' => 'required|string|max:100|unique:settings,key',
        'value' => 'nullable|array',
    ]);

    $setting = Setting::create($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم إضافة الإعداد بنجاح',
        'data' => $setting
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $setting = Setting::findOrFail($id);

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب الإعداد بنجاح',
        'data' => $setting
    ], 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $setting = Setting::findOrFail($id);

    $validated = $request->validate([
        'key' => 'sometimes|string|max:100|unique:settings,key,' . $setting->id,
        'value' => 'nullable|array',
    ]);

    $setting->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم تحديث الإعداد بنجاح',
        'data' => $setting
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $setting = Setting::findOrFail($id);

    $setting->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'تم حذف الإعداد بنجاح'
    ], 200);
}
}
