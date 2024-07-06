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

    public static function getOrderStatusTitle(int $status): string
    {
        return match ($status) {
            self::Pending->value => 'Chờ xác nhận',
            self::Confirmed->value => 'Đã xác nhận',
            self::Delivery->value => 'Đang vận chuyển',
            self::Done->value => 'Đã hoàn thành',
            self::Cancelled->value => 'Đã hủy',
            default => 'N/A'
        };
    }
}
