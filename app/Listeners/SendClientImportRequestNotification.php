<?php

namespace App\Listeners;


use App\Events\ImportRequestReviewed;
use App\Models\InternalNotification;

class SendClientImportRequestNotification
{
    public function handle(ImportRequestReviewed $event): void
    {
        $importRequest = $event->importRequest;
        $user = $importRequest->user;

        if (!$user) return;

        $isRejected = $event->action === 'reject';

        $title = [
            'ar' => $isRejected ? 'تم رفض طلب الاستيراد' : 'تم تسعير طلب الاستيراد',
            'en' => $isRejected ? 'Import Request Rejected' : 'Import Request Priced',
        ];

        $message = [
            'ar' => $isRejected
                ? "عذراً، تم رفض طلبك رقم #{$importRequest->id}. السبب: " . ($importRequest->rejection_reason ?? 'لا يوجد سبب محدد')
                : "تم تسعير طلب الاستيراد رقم #{$importRequest->id} وإرسال عرض السعر بقيمة {$importRequest->offered_grand_total} {$importRequest->currency}.",
            'en' => $isRejected
                ? "Sorry, your import request #{$importRequest->id} was rejected."
                : "Your import request #{$importRequest->id} has been priced with total {$importRequest->offered_grand_total} {$importRequest->currency}.",
        ];

        InternalNotification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'message' => $message,
            'type'    => 'import_request_reviewed',
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }
}
