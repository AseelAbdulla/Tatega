<?php

namespace App\Events;

use App\Models\ImportRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportRequestReviewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ImportRequest $importRequest,
        public string $action // 'approve' أو 'reject'
    ) {}
}
