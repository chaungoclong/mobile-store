<?php

declare(strict_types=1);

namespace App\Enums;

enum TimeRange: string
{
    use EnumToArray;

    case ToDay = 'today';
    case ThisWeek = 'thisWeek';
    case ThisMonth = 'thisMonth';
    case ThisYear = 'thisYear';
    case Yesterday = 'yesterday';
    case LastWeek = 'lastWeek';
    case LastMonth = 'lastMonth';
    case LastYear = 'lastYear';
    case Custom = 'custom';

    public static function PresentTimeRanges(): array
    {
        return [
            self::ToDay->value,
            self::ThisWeek->value,
            self::ThisMonth->value,
            self::ThisYear->value,
        ];
    }

    public static function PastTimeRanges(): array
    {
        return [
            self::Yesterday->value,
            self::LastWeek->value,
            self::LastMonth->value,
            self::LastYear->value,
        ];
    }

    public static function YearTypes(): array
    {
        return [
            self::ThisYear->value,
            self::LastYear->value
        ];
    }

    public static function DayTypes(): array
    {
        return [
            self::ToDay->value,
            self::Yesterday->value
        ];
    }
}
