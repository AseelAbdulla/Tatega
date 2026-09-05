<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}


    /*
    |--------------------------------------------------------------------------
    | PUBLIC - APPROVED REVIEWS
    |--------------------------------------------------------------------------
    */

    public function approved(
        Request $request
    ) {

        $page = (int) $request->get(
            'page',
            1
        );

        $sort = $request->get(
            'sort',
            'latest'
        );

        return ReviewResource::collection(

            $this->reviewService->approved(
                $page,
                $sort
            )

        );
    }


    /*
    |--------------------------------------------------------------------------
    | PUBLIC - STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreReviewRequest $request
    ) {

        $review =
            $this->reviewService->store(
                $request->validated()
            );

        return response()->json([
            'status' => 'success',

            'message' =>
                'تم إرسال تقييمك بنجاح، وسيظهر بعد مراجعته واعتماده.',

            'data' =>
                new ReviewResource($review),
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - INDEX
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {

        $page = (int) $request->get(
            'page',
            1
        );

        $status = $request->get(
            'status',
            'all'
        );

        return ReviewResource::collection(

            $this->reviewService->index(
                $page,
                $status
            )

        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        Review $review
    ) {

        return new ReviewResource(
            $this->reviewService->show(
                $review
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateReviewRequest $request,
        Review $review
    ) {

        $review =
            $this->reviewService->update(
                $review,
                $request->validated()
            );

        return new ReviewResource(
            $review
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Review $review
    ) {

        $this->reviewService->destroy(
            $review
        );

        return response()->json([
            'status' => 'success',

            'message' =>
                'تم حذف التقييم بنجاح.',
        ]);
    }
}