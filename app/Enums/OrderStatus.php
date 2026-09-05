<?php


namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';

    case ACCEPTED = 'accepted';

    case PREPARING = 'preparing';

    case SHIPPED = 'shipped';

    case DELIVERED = 'delivered';

    case CANCELLED = 'cancelled';

    case REJECTED = 'rejected';
}
