<?php

namespace App\Listeners;

use App\Events\ImportRequestCreated;
use App\Models\InternalNotification;
use App\Models\User;

class SendImportRequestNotification
{
    public function handle(ImportRequestCreated $event): void
    {
        $importRequest = $event->importRequest;
        $user = $importRequest->user;

        // جلب المدراء
        $adminUsers = User::role('admin')->get();

        foreach ($adminUsers as $admin) {
            InternalNotification::create([
                'user_id' => $admin->id,
                'title'   => [
                    'ar' => 'طلب استيراد جديد',
                    'en' => 'New Import Request'
                ],
                'message' => [
                    'ar' => "قام العميل ({$user->name}) بتقديم طلب استيراد جديد رقم #{$importRequest->id}",
                    'en' => "Client ({$user->name}) submitted a new import request #{$importRequest->id}"
                ],
                'type'    => 'import_request',
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }
    }
}
