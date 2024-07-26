<?php

declare(strict_types=1);

namespace App\Enums;

enum StockStatus: string
{
    case IN_STOCK = 'in_stock';
    case OUT_OF_STOCK = 'out_of_stock';
    case RUNNING_OUT = 'running_out';

    public function label(): string
    {
        return match ($this) {
            self::IN_STOCK => 'Còn Hàng',
            self::OUT_OF_STOCK => 'Hết Hàng',
            self::RUNNING_OUT => 'Sắp Hết Hàng'
        };
    }
}
