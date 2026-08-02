<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{

    public function index()
    {
        return Review::with([
            'user',
            'product'
        ])->get();
    }


    public function store(array $data)
    {
        return Review::create($data);
    }


    public function show(Review $review)
    {
        return $review->load([
            'user',
            'product'
        ]);
    }


    public function update(Review $review, array $data)
    {
        $review->update($data);

        return $review;
    }


    public function destroy(Review $review)
    {
        $review->delete();

        return true;
    }

}