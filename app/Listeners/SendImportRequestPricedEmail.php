<?php

namespace App\Listeners;


use App\Events\ImportRequestReviewed;
use App\Mail\ImportRequestPricedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendImportRequestPricedEmail implements ShouldQueue
{
    public function handle(ImportRequestReviewed $event): void
    {
        $importRequest = $event->importRequest;

        if ($importRequest->user && $importRequest->user->email) {
            Mail::to($importRequest->user->email)
                ->send(new ImportRequestPricedMail($importRequest, $event->action));
        }
    }
}
