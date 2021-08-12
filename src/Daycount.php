<?php

declare(strict_types=1);

namespace davekok\calendar;

use OutOfRangeException;

class Daycount
{
    public const OF_WEEK        = 7;
    public const OF_NORMAL_YEAR = 365;
    public const OF_LEAP_YEAR   = 366;
    public const OF_3YRS        = 3 * self::OF_NORMAL_YEAR;
    public const OF_4YRS        = self::OF_3YRS + self::OF_LEAP_YEAR;
    public const OF_99YRS       = 24 * self::OF_4YRS + self::OF_3YRS;
    public const OF_100YRS      = self::OF_99YRS + self::OF_NORMAL_YEAR;
    public const OF_399YRS      = 3 * self::OF_100YRS + self::OF_99YRS;
    public const OF_400YRS      = self::OF_399YRS + self::OF_LEAP_YEAR;
    public const OF_1969YRS     = 4 * self::OF_400YRS
                                  + 3 * self::OF_100YRS
                                  + 17 * self::OF_4YRS
                                  + self::OF_NORMAL_YEAR;
    public const OF_9999YRS     = 24 * self::OF_400YRS
                                  + 3 * self::OF_100YRS
                                  + 24 * self::OF_4YRS
                                  + self::OF_3YRS;

    /**
     * The number of days in a month.
     */
    public static function ofMonth(int $month, bool $isLeapYear): int
    {
        Calendar::checkMonth($month);

        if ($month === 2) {
            return $isLeapYear ? 29 : 28;
        }

        /**
         * m = month (1,3..12)
         * a = result of first expression
         * b = result of second expression
         * c = result of third expression
         * d = result of fourth expression
         * e = result of fifth expression and return value
         *
         *  m & 8 = a   a >> 3 = b    m & 1 = c   b ^ c = d   30 + d = e
         * --------------------------------------------------------------
         *  1 & 8 = 0   0 >> 3 = 0    1 & 1 = 1   0 ^ 1 = 1   30 + 1 = 31
         *  3 & 8 = 0   0 >> 3 = 0    3 & 1 = 1   0 ^ 1 = 1   30 + 1 = 31
         *  4 & 8 = 0   0 >> 3 = 0    4 & 1 = 0   0 ^ 0 = 0   30 + 0 = 30
         *  5 & 8 = 0   0 >> 3 = 0    5 & 1 = 1   0 ^ 1 = 1   30 + 1 = 31
         *  6 & 8 = 0   0 >> 3 = 0    6 & 1 = 0   0 ^ 0 = 0   30 + 0 = 30
         *  7 & 8 = 0   0 >> 3 = 0    7 & 1 = 1   0 ^ 1 = 1   30 + 1 = 31
         *  8 & 8 = 8   8 >> 3 = 1    8 & 1 = 0   1 ^ 0 = 1   30 + 1 = 31
         *  9 & 8 = 8   8 >> 3 = 1    9 & 1 = 1   1 ^ 1 = 0   30 + 0 = 30
         * 10 & 8 = 8   8 >> 3 = 1   10 & 1 = 0   1 ^ 0 = 1   30 + 1 = 31
         * 11 & 8 = 8   8 >> 3 = 1   11 & 1 = 1   1 ^ 1 = 0   30 + 0 = 30
         * 12 & 8 = 8   8 >> 3 = 1   12 & 1 = 0   1 ^ 0 = 1   30 + 1 = 31
         */
        return 30 + ((($month & 8) >> 3) ^ ($month & 1));
    }

    /**
     * The number of days in a year.
     */
    public static function ofYear(bool $isLeapYear): int
    {
        return $isLeapYear ? self::OF_LEAP_YEAR : self::OF_NORMAL_YEAR;
    }
}
