<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: int
{
    case Unpaid = 0;
    case Paid = 1;

    case Failed = 2;
}
