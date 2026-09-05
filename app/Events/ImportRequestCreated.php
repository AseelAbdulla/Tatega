<?php

namespace App\Events;

use App\Models\ImportRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportRequestCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public ImportRequest $importRequest) {}
}
