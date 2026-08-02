<?php


namespace App\Enums;

enum PaymentMethod: string
{
    case CASH_ON_DELIVERY = 'cash_on_delivery';

    case WALLET = 'wallet';
}

