<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
{
    $reviews = Review::with(['user', 'product'])->get();

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب المراجعات بنجاح',
        'data' => $reviews
    ], 200);

}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'user_id' => 'nullable|exists:users,id',
        'visitor_name' => 'nullable|string|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string',
        'status' => 'nullable|string|max:50',
    ]);

    $review = Review::create($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم إضافة المراجعة بنجاح',
        'data' => $review
    ], 201);
}

    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $review = Review::with(['user', 'product'])
                    ->findOrFail($id);

    return response()->json([
        'status' => 'success',
        'message' => 'تم جلب المراجعة بنجاح',
        'data' => $review
    ], 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $review = Review::findOrFail($id);

    $validated = $request->validate([
        'product_id' => 'sometimes|exists:products,id',
        'user_id' => 'nullable|exists:users,id',
        'visitor_name' => 'nullable|string|max:255',
        'rating' => 'sometimes|integer|min:1|max:5',
        'comment' => 'nullable|string',
        'status' => 'nullable|string|max:50',
    ]);

    $review->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'تم تحديث المراجعة بنجاح',
        'data' => $review
    ], 200);
}
    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
{
    $review = Review::findOrFail($id);

    $review->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'تم حذف المراجعة بنجاح'
    ], 200);
}
}
