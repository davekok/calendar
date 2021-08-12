<?php

declare(strict_types=1);

namespace davekok\calendar;

/**
 * An implementation of a proleptic gregorian calendar starting at
 * Mon, 01 Jan 0001 00:00:00 and ending at Fri, 31 Dec 9999 23:59:59.
 *
 * Years outside this range are not supported.
 *
 * In accordance with ISO 8601, Monday is considered the first day of the week.
 * Also ISO 8601 week numbering is used.
 *
 * Timestamps are valid in the range of 000000000001 to 315537897600 inclusive.
 *
 * - 000000000001 = Mon, 01 Jan 0001 00:00:00
 * - 315537897600 = Fri, 31 Dec 9999 23:59:59
 *
 * Any timestamp outside this range is considered invalid, 000000000000 is
 * left intentially invalid to easily test for errors.
 *
 * Converting a timestamp to a unix timestamp is as easy as substracting the
 * difference between year 1 and the year 1970 (in seconds).
 *
 * Leap seconds are ignored, leap seconds are not regulary and are forecasted
 * only a few weeks in advance. Supporting leap seconds is thus not possible.
 * However, since most computer system clocks experience some drift anyways. It
 * is not considered a problem as synchronising the clock regularly will always
 * be necessary.
 */
class Calendar
{
    public static function checkYear(int $year): void
    {
        if ($year < 1 || $year > 9999) {
            throw new OutOfRangeException("A year must be in the range of 1..9999");
        }
    }

    public static function checkYearday(int $yearday, bool $isLeapYear): void
    {
        if ($isLeapYear) {
            if ($yearday < 1 || $yearday > 366) {
                throw new OutOfRangeException(
                    "A yearday must be in the range of 1..365 for leap years."
                );
            }
            return;
        }

        if ($yearday < 1 || $yearday > 365) {
            throw new OutOfRangeException("A yearday must be in the range of 1..365 for regular years.");
        }
    }

    public static function checkMonth(int $month): void
    {
        if ($month < 1 || $month > 12) {
            throw new OutOfRangeException("A month must be in the range of 1..12");
        }
    }

    public static function checkDay(int $month, int $day, bool $isLeapYear): void
    {
        $daycount = Daycount::ofMonth($month, $isLeapYear);
        if ($day < 1 || $day > $daycount) {
            if ($month === 2 && $isLeapYear) {
                throw new OutOfRangeException(
                    "A day must be in the range of 1..$daycount for month $month when it is a leap year"
                );
            }

            throw new OutOfRangeException(
                "A day must be in the range of 1..$daycount for month $month"
            );
        }
    }

    public static function checkWeek(int $week): void
    {
        if ($week < 1 || $week > 53) {
            throw new OutOfRangeException("A week must be in the range of 1..53");
        }
    }

    public static function checkWeekday(int $weekday): void
    {
        if ($weekday < 1 || $weekday > 7) {
            throw new OutOfRangeException("A weekday must be in the range of 1..7");
        }
    }

    public static function checkDaystamp(int $daystamp): void
    {
        if ($daystamp < 1 || $daystamp > Daycount::OF_9999YRS) {
            throw new OutOfRangeException("A daystamp must be in the range of 1.." . Daycount::OF_9999YRS);
        }
    }

    public static function checkTimestamp(int $timestamp): void
    {
        if ($timestamp < 1 || $timestamp > Timecount::OF_9999YRS) {
            throw new OutOfRangeException(
                "A timestamp must be in the range of 1.." . Timecount::OF_9999YRS
            );
        }
    }

    public static function checkUnixtimestamp(int $unixtimestamp): void
    {
        $min = 1 - Timecount::OF_1969YRS;
        $max = Timecount::OF_9999YRS - Timecount::OF_1969YRS;
        if ($unixtimestamp < $min || $unixtimestamp > $max) {
            throw new OutOfRangeException("A unixtimestamp must be in the range of $min..$max");
        }
    }

    public static function checkHour(int $hour): void
    {
        if ($hour < 0 || $hour > 23) {
            throw new OutOfRangeException("A hour must be in the range of 0..23");
        }
    }

    public static function checkMinute(int $minute): void
    {
        if ($minute < 0 || $minute > 59) {
            throw new OutOfRangeException("A minute must be in the range of 0..59");
        }
    }

    public static function checkSecond(int $second): void
    {
        if ($second < 0 || $second > 59) {
            throw new OutOfRangeException("A second must be in the range of 0..59");
        }
    }

    public static function isLeapYear(int $year): bool
    {
        Calendar::checkYear($year);
        return (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0));
    }


    public static function monthToYearday(int $month, bool $isLeapYear): int
    {
        Calendar::checkMonth($month);
        $yearday = 1;
        for ($m = 1; $m < $month; ++$m) {
            $yearday += Daycount::ofMonth($m, $isLeapYear);
        }
        return $yearday;
    }

    /**
     * Convert year to daystamp.
     */
    public static function yearToDaystamp(int $year): int
    {
        return Daycount::OF_400YRS * (--$year / 400)
            + Daycount::OF_100YRS * (($year %= 400) / 100)
            + Daycount::OF_4YRS * (($year %= 100) / 4)
            + Daycount::OF_NORMAL_YEAR * ($year % 4);
    }

    /**
     * Converts daystamp to a yearday.
     */
    public static function daystampToYearday(int $daystamp): Yearday
    {
        /* Calendar starts at year one, not zero. */
        /* Year starts at day one, not zero. */
        Daycount::check($daystamp);

        $year = 1 + 400 * ($daystamp / Daycount::OF_400YRS);
        $daystamp %= Daycount::OF_400YRS;
        if ($daystamp >= Daycount::OF_399YRS) {
            return new Yearday(
                year: $year + 399,
                yearday: 1 + $daystamp - Daycount::OF_399YRS,
                isLeapYear: true
            );
        }

        $year += 100 * ($daystamp / Daycount::OF_100YRS);
        $daystamp %= Daycount::OF_100YRS;
        if ($daystamp >= Daycount::OF_99YRS) {
            return new Yearday(
                year: $year + 99,
                yearday: 1 + $daystamp - Daycount::OF_99YRS,
                isLeapYear: false
            );
        }

        $year += 4 * ($daystamp / Daycount::OF_4YRS);
        $daystamp %= Daycount::OF_4YRS;
        if ($daystamp >= Daycount::OF_3YRS) {
            return new Yearday(
                year: $year + 3,
                yearday: 1 + $daystamp - Daycount::OF_3YRS,
                isLeapYear: true
            );
        }
        return new Yearday(
            year: $year + $daystamp / Daycount::OF_NORMAL_YEAR,
            yearday: 1 + $daystamp % Daycount::OF_NORMAL_YEAR,
            isLeapYear: false
        );
    }

    public static function yeardayToMonth(int $yearday, bool $isLeapYear): Monthday
    {
        Calenday::checkYearday($yearday, $isLeapYear);
        $month = 0;
        --$yearday;
        while ($yearday >= ($totaldays = Daycount::ofMonth(++$month, $isLeapYear))) {
            $yearday -= $totaldays;
        }
        return Monthday(
            month: $month;
            monthday: $yearday + 1
        );
    }

    public static function daystampToWeekday(int $daystamp): int
    {
        return 1 + $daystamp % Daycount::OF_WEEK;
    }

    public static function weeknr(int $year, int $yearday, int $weekday): int
    {
        $week = (10 + $yearday - $weekday) / 7;
        if ($week > 0 && $week < 53) {
            return $week;
        }

        if ($week === 0) {
            $daycountOfYesteryear = Daycount::ofYear(self::isLeapYear($year - 1));
            $dec31Yesteryear = self::daystampToWeekday(
                self::yearToDaystamp($year - 1) + $daycountOfYesteryear - 1
            );
            return (10 + $daycountOfYesteryear - $dec31Yesteryear) / 7;
        }

        $weekdayJan1OfNextyear = self::daystampToWeekday(
            self::yearToDaystamp($year) + Daycount::ofYear(self::isLeapYear($year))
        );
        if ((10 + 1 - $weekdayJan1OfNextyear) / 7) {
            return 1;
        }

        return 53;
    }

    public static function weekToYearday(int $week, int $weekday, int $weekdayJan4): int
    {
        return $week * 7 + $weekday - ($weekdayJan4 + 3);
    }

    public static function timestampToDaystamp(int $timestamp): int
    {
        return --$timestamp / Timecount::OF_DAY;
    }

    public static function timestampToTime(int $timestamp): Time
    {
        return new Time(
            hour: ($timestamp %= Timecount::OF_DAY) / Timecount::OF_HOUR;
            minute: ($timestamp %= Timecount::OF_HOUR) / Timecount::OF_MINUTE;
            second: ($timestamp %= Timecount::OF_MINUTE) / Timecount::OF_SECOND;
        );
    }

    public static function unixtimestampToTimestamp(int $unixtimestamp): int
    {
        self::checkUnixtimestamp($unixtimestamp);
        return $unixtimestamp + Timecount::OF_1969YRS;
    }

    public static function timestampToUnixtimestamp(int $timestamp): int
    {
        self::checkTimestamp($timestamp);
        return $timestamp - Timecount::OF_1969YRS;
    }

    public static function timestampToDateTime(int $timestamp): DateTime
    {
        self::checkTimestamp($timestamp);

        $time = self::timestampToTime($timestamp);
        $daystamp = self::timestampToDaystamp($timestamp);
        $yearday = self::daystampToYearday($daystamp);
        $monthday = self::yeardayToMonthday($yearday->day, $yearday->isLeapYear);
        $weekday = self::daystampToWeekday($daystamp);
        $week = self::weeknr($yearday->year, $yearday->day, $weekday);

        return new DateTime(
            year: $yearday->year,
            isLeapYear: $yearday->isLeapYear,
            month: $monthday->month,
            week: $week,
            weekday: $weekday,
            day: $monthday->day,
            hour: $time->hour,
            minute: $time->minute,
            second: $time->second
        );
    }

    public static function dateTimeToTimestamp(DateTime $dateTime): int
    {
        $timecount = self::timeToTimecount(
            $dateTime->hour ?? 0,
            $dateTime->minute ?? 0,
            $dateTime->second ?? 0
        );

        if ($dateTime->year !== null && $dateTime->yearday !== null) {
            return $timecount
                + self::yeardayToTimestamp($dateTime->year, $dateTime->yearday);
        }

        if ($dateTime->year !== null && $dateTime->month !== null && $dateTime->day !== null) {
            $isLeapYear = self::isLeapYear($dateTime->year);
            self::checkDay($month, $day, $isLeapYear);
            $yearday = self::monthToYearday($month, $isLeapYear);
            return $timecount
                + self::yeardayToTimestamp($dateTime->year, $yearday);
        }

        if ($dateTime->year !== null && $dateTime->week !== null && $dateTime->weekday !== null) {
            $yearday = self::weekToYearday($dateTime->week, $dateTime->weekday, 1)
            return $timecount
                + self::yeardayToTimestamp($dateTime->year, $yearday);
        }
    }

    public static function createDateTimeFromString(string $datetime): DateTime
    {
    }

    public static function yeardayToTimestamp(int $year, int $yearday): int
    {
        $isLeapYear = self::isLeapYear($year);
        self::checkYear($year);
        self::checkYearday($year, $isLeapYear);
        return 1 + (self::yearToDaystamp($year) + $yearday - 1) * Timecount::OF_DAY;
    }

    public static function timeToTimecount(int $hour, int $minute, int $second): int
    {
        self::checkHour($hour);
        self::checkMinute($minute);
        self::checkSecond($second);
        return $hour * Timecount::OF_HOUR
            + $minute * Timecount::OF_MINUTE
            + $second * Timecount::OF_SECOND;
    }
}
