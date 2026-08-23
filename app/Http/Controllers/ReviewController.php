<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    public function index()
    {
        return ReviewResource::collection(
            $this->reviewService->index()
        );
    }

    public function store(StoreReviewRequest $request)
    {
        $review = $this->reviewService->store(
            $request->validated()
        );

        return new ReviewResource($review);
    }

    public function show(Review $review)
    {
        return new ReviewResource(
            $this->reviewService->show($review)
        );
    }

    public function update(
        UpdateReviewRequest $request,
        Review $review
    ) {
        $review = $this->reviewService->update(
            $review,
            $request->validated()
        );

        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->reviewService->destroy($review);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المراجعة بنجاح'
        ]);
    }
}