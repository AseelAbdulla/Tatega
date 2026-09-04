<?php

namespace App\Mail;

use App\Models\ImportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportRequestPricedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ImportRequest $importRequest,
        public string $action
    ) {}

    public function build()
    {
        $subject = $this->action === 'reject'
            ? "تحديث بشأن طلب الاستيراد رقم #{$this->importRequest->id}"
            : "تم تسعير طلب الاستيراد الخاص بك رقم #{$this->importRequest->id}";

        return $this->subject($subject)
                    ->markdown('emails.import_requests.reviewed');
    }
}
