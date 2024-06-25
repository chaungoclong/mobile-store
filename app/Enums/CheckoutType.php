<?php

declare(strict_types=1);

namespace App\Enums;

enum CheckoutType: string
{
    case BuyNow = 'buy_now';
    case BuyCart = 'buy_cart';
}
