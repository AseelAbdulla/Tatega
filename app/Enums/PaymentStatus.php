<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';

    case PENDING_REVIEW = 'pending_review';

    case PAID = 'paid';

    case REJECTED = 'rejected';
}
