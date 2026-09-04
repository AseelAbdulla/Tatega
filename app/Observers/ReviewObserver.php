<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\InternalNotification;
use App\Models\User;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $recipients = User::role(['admin', 'employee'], 'sanctum')->get();

        // اسم الكاتب (سواء كان مستخدم مسجل أو زائر)
        $authorName = $review->visitor_name ?? $review->user?->name ?? 'زائر';
        $productTitle = $review->product?->name ?? 'منتج';

        foreach ($recipients as $recipient) {
            InternalNotification::create([
                'user_id' => $recipient->id,
                'type' => 'review',
                'title' => [
                    'ar' => 'تقييم جديد منتظر للمراجعة',
                    'en' => 'New Pending Review'
                ],
                'message' => [
                    'ar' => "أضاف {$authorName} تقييماً ({$review->rating}/5) على: {$productTitle}",
                    'en' => "{$authorName} left a ({$review->rating}/5) review on: {$productTitle}"
                ],
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
