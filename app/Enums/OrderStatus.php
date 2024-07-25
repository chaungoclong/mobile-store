<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: int
{
    use EnumToArray;

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

    public static function uncancellableStatus(): array
    {
        return [
            self::Done->value,
            self::Cancelled->value,
            self::Delivery->value
        ];
    }

    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return match ($this) {
            self::Pending => in_array($newStatus, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($newStatus, [self::Delivery, self::Cancelled]),
            self::Delivery => in_array($newStatus, [self::Done, self::Cancelled]),
            self::Done, self::Cancelled => false,
        };
    }

    public function toHtml(): string
    {
        return sprintf('<span class="%s">%s</span>', $this->cssClass(), $this->label());
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Pending => 'label label-primary',
            self::Confirmed => 'label label-info',
            self::Delivery => 'label label-warning',
            self::Done => 'label label-success',
            self::Cancelled => 'label label-danger',
        };
    }

    public function buttonClass(): string
    {
        return match ($this) {
            self::Pending => 'btn btn-primary',
            self::Confirmed => 'btn btn-info',
            self::Delivery => 'btn btn-warning',
            self::Done => 'btn btn-success',
            self::Cancelled => 'btn btn-danger',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Đang chờ',
            self::Confirmed => 'Đã xác nhận',
            self::Delivery => 'Đang vận chuyển',
            self::Done => 'Đã giao',
            self::Cancelled => 'Đã hủy',
        };
    }
}
