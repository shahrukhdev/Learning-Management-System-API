<?php

namespace App\Enums;

use Carbon\Carbon;

/**
 * Attendance periods shared by the student dashboard and the student
 * attendance page, so both always cover exactly the same dates.
 */
enum AttendancePeriod: string
{
    case SEVEN_DAYS = '7days';
    case THIRTY_DAYS = '30days';
    case THREE_MONTHS = '3months';

    public const DEFAULT = self::SEVEN_DAYS;

    /**
     * First day of the period (inclusive). The period always ends today.
     *
     * 7days   = today and the 6 days before it
     * 30days  = today and the 29 days before it
     * 3months = from the 1st of the month two months ago (whole calendar
     *           months, the same as the admin / teacher dashboard chart)
     */
    public function startDate(?Carbon $today = null): Carbon
    {
        $today = ($today ?? now())->copy()->startOfDay();

        return match ($this) {
            self::SEVEN_DAYS => $today->subDays(6),
            self::THIRTY_DAYS => $today->subDays(29),
            self::THREE_MONTHS => $today->startOfMonth()->subMonths(2),
        };
    }

    public static function fromValue(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::DEFAULT;
    }
}
