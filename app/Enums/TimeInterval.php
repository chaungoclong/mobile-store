<?php

declare(strict_types=1);

namespace App\Enums;

enum TimeInterval: string
{
    case Hour = 'hour';
    case Day = 'day';
    case Month = 'month';
    case Year = 'year';
}
