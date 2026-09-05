<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\InternalNotification;
use App\Models\User;

class OrderObserver
{
    public function created(Order $order): void
    {
        // جلب المدراء والموظفين المسموح لهم باستلام الإشعارات
        $recipients = User::role(['admin', 'employee'], 'sanctum')->get();

        // اسم العميل صاحب الطلب
        $customerName = $order->customer_name ?? $order->user?->name ?? 'عميل';

        foreach ($recipients as $recipient) {
            InternalNotification::create([
                'user_id' => $recipient->id,
                'type' => 'order',
                'title' => [
                    'ar' => 'طلب جديد #' . $order->id,
                    'en' => 'New Order #' . $order->id
                ],
                'message' => [
                    'ar' => "تم تقديم طلب جديد بواسطة {$customerName} بقيمة {$order->total_price} ر.ي",
                    'en' => "New order placed by {$customerName} for amount {$order->total_price}"
                ],
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
