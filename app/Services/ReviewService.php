<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC - APPROVED REVIEWS
    |--------------------------------------------------------------------------
    */

    public function approved(
        int $page = 1,
        string $sort = 'latest'
    ) {
        $query = Review::query()
            ->approved()
            ->with([
                'user',
                'product',
            ]);

        switch ($sort) {

            case 'highest':

                $query
                    ->orderByDesc('rating')
                    ->latest();

                break;

            case 'lowest':

                $query
                    ->orderBy('rating')
                    ->latest();

                break;

            default:

                $query->latest();

                break;
        }

        return $query->paginate(
            6,
            ['*'],
            'page',
            $page
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - ALL REVIEWS
    |--------------------------------------------------------------------------
    */

    public function index(
        int $page = 1,
        string $status = 'all'
    ) {
        $query = Review::query()
            ->with([
                'user',
                'product',
            ]);

        if (
            in_array(
                $status,
                [
                    'pending',
                    'approved',
                    'rejected',
                ]
            )
        ) {
            $query->where(
                'status',
                $status
            );
        }

        return $query
            ->latest()
            ->paginate(
                10,
                ['*'],
                'page',
                $page
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
        return $review->load([
            'user',
            'product',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PUBLIC - STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        array $data
    ) {
        return Review::create([

            'product_id' =>
                $data['product_id'] ?? null,

            'user_id' =>
                auth()->id(),

            'visitor_name' =>
                $data['visitor_name'],

            'visitor_email' =>
                $data['visitor_email'],

            'rating' =>
                $data['rating'],

            'comment' =>
                $data['comment'],

            // مهم:
            // لا يظهر مباشرة
            'status' =>
                'pending',

            'admin_note' =>
                null,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Review $review,
        array $data
    ) {

        $review->update([

            'status' =>
                $data['status']
                ?? $review->status,

            'admin_note' =>
                $data['admin_note']
                ?? $review->admin_note,

            'visitor_name' =>
                $data['visitor_name']
                ?? $review->visitor_name,

            'visitor_email' =>
                $data['visitor_email']
                ?? $review->visitor_email,

            'rating' =>
                $data['rating']
                ?? $review->rating,

            'comment' =>
                $data['comment']
                ?? $review->comment,
        ]);

        return $review->fresh([
            'user',
            'product',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Review $review
    ) {
        return $review->delete();
    }
}