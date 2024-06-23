<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: int
{
    case Pending = 1;
    case Delivery = 5;
    case Confirmed = 2;
    case Done = 3;
    case Cancelled = 4;
}
