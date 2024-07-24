<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: int
{
    case Unpaid = 0;
    case Paid = 1;

    case Failed = 2;

    public function toHtml(): string
    {
        return sprintf('<span class="%s">%s</span>', $this->cssClass(), $this->labelText());
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Unpaid => 'label label-warning',
            self::Paid => 'label label-success',
            self::Failed => 'label label-danger',
        };
    }

    public function labelText(): string
    {
        return match ($this) {
            self::Unpaid => 'Chưa thanh toán',
            self::Paid => 'Đã thanh toán',
            self::Failed => 'Thanh toán thất bại',
        };
    }
}
