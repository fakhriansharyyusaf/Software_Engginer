<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

/**
 * Central clock for SEAPEDIA.
 *
 * The real deployment clock cannot be changed, so for demo purposes
 * (SLA / overdue simulation) we keep a "simulated day offset" in the
 * settings table. Admin::simulateNextDay() increments it. Every part
 * of the system that needs "now" for business-logic purposes (SLA due
 * dates, overdue checks) must use TimeService::now() instead of
 * Carbon::now()/now() directly.
 */
class TimeService
{
    private const KEY = 'time_offset_days';

    public static function now(): Carbon
    {
        $offsetDays = (int) Setting::get(self::KEY, 0);

        return Carbon::now()->addDays($offsetDays);
    }

    public static function offsetDays(): int
    {
        return (int) Setting::get(self::KEY, 0);
    }

    public static function simulateNextDay(): int
    {
        $offset = self::offsetDays() + 1;
        Setting::set(self::KEY, $offset);

        return $offset;
    }

    public static function reset(): void
    {
        Setting::set(self::KEY, 0);
    }
}
